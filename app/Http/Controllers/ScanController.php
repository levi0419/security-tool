<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Http;
use App\Events\ScanProgressUpdated;
use Dompdf\Dompdf;
use Dompdf\Options;


class ScanController extends Controller
{
    public function index()
    {
        return view("index");
    }

    public function scan(Request $request) {
        return view("scan");

    }
    

    public function scanTest(Request $request)
    {
        // Validate the URL
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');
        $results = [];

        // 1. SSL/TLS Certificate Check
        $results['Check SSL/TLS Certificate'] = $this->checkSSLCertificate($url);

        // 2. HTTP Security Headers
        $results['Check HTTP Security Headers'] = $this->checkHTTPHeaders($url);

        // 3. Shodan Open Ports (Using Nmap locally)
        $results['Shodan Open Ports'] = $this->checkOpenPorts(parse_url($url, PHP_URL_HOST));

        // 4. Known Vulnerabilities
        $results['Known Vulnerabilities'] = $this->checkKnownVulnerabilities($url);

        // 5. XSS Scan
        $results['XSS Scan'] = $this->checkXSS($url);

        // 6. SQL Injection
        $results['SQL Injection'] = $this->checkSQLi($url);

         // Store results in the session
        session(['scan_results' => $results]);



        // Return scan results view
        return view('results', ['results' => $results]);
    }

    private function checkSSLCertificate($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $apiUrl = 'https://api.ssllabs.com/api/v3/analyze?host=' . $host;

        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                return $response->json();
            } else {
                return 'Error: Unable to fetch SSL details';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function checkHTTPHeaders($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // We only need the headers
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
        $headers = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $headers;
    }

    private function checkOpenPorts($host)
    {
        // Path to the nmap binary; adjust if necessary
        $nmapPath = '/usr/bin/nmap';

        // Escape shell command and arguments
        $command = escapeshellcmd($nmapPath) . ' -p- ' . escapeshellarg($host);

        // Execute the command and capture the output
        $output = shell_exec($command);

        // Check if the command execution was successful
        if ($output === null) {
            // Get the last error, if any
            $error = error_get_last();

            // Check if error_get_last() returned valid data
            if ($error !== null && isset($error['message'])) {
                return 'Error: Unable to run nmap command. ' . $error['message'];
            } else {
                return 'Error: Unable to run nmap command, and no additional error information is available.';
            }
        }

        return $output;
    }

    private function checkKnownVulnerabilities($url)
    {
        // Extract host from the URL for the query
        $host = parse_url($url, PHP_URL_HOST);
        
        // NVD API URL to query vulnerabilities
        $apiUrl = 'https://services.nvd.nist.gov/rest/json/cve/1.0?keyword=' . urlencode($host);

        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']['CVE_Items'])) {
                    $vulnerabilities = [];
                    foreach ($data['result']['CVE_Items'] as $item) {
                        $vulnerabilities[] = [
                            'id' => $item['cve']['CVE_data_meta']['ID'],
                            'description' => $item['cve']['description']['description_data'][0]['value'],
                        ];
                    }
                    return $vulnerabilities;
                } else {
                    return 'No vulnerabilities found for the specified host.';
                }
            } else {
                return 'Error: Unable to fetch vulnerability details from NVD';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }


    private function checkXSS($url)
    {
        $testPayloads = [
            "<script>alert('XSS')</script>",
            "<img src=x onerror=alert('XSS')>",
            "'<svg onload=alert('XSS')>"
        ];
        
        $results = [];
        
        foreach ($testPayloads as $payload) {
            $testUrl = $url . "?param=" . urlencode($payload);
            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $results[] = 'Error: ' . curl_error($ch);
            } else {
                // Checking for XSS payload in response
                if (strpos($response, 'XSS') !== false || preg_match('/alert\(/', $response)) {
                    $results[] = "Potential XSS Vulnerability Detected: $testUrl";
                }
            }
            curl_close($ch);
        }
        
        return $results;
    }

    private function checkSQLi($url)
    {
        // List of SQL injection payloads
        $testPayloads = [
            "' OR '1'='1",
            "' OR 'a'='a",
            "' UNION SELECT NULL, NULL, NULL--",
            "' AND 1=CONVERT(int, CHAR(1))--",
            "' AND 1=1",
            "' AND 1=2",
            '" OR "a"="a',
            '" UNION SELECT 1,2,3--'
        ];

        $results = [];

        foreach ($testPayloads as $payload) {
            $testUrl = $url . "?param=" . urlencode($payload);
            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $results[] = 'Error: ' . curl_error($ch);
            } else {
                // Check for SQL error messages or signs of SQL injection vulnerability
                if (strpos($response, 'SQL') !== false || preg_match('/error|exception/', $response)) {
                    $results[] = "Potential SQL Injection Vulnerability Detected: $testUrl";
                }
            }
            curl_close($ch);
        }

        return $results;
    }

    public function downloadResults(Request $request)
    {
        // Retrieve results from the session
        $results = session('scan_results', []);
    
        // Initialize DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
    
        $dompdf = new Dompdf($options);
    
        // Load HTML content
        $view = view('pdf', compact('results'))->render();
        $dompdf->loadHtml($view);
    
        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
    
        // Render PDF (first pass)
        $dompdf->render();
    
        // Stream the generated PDF
        return $dompdf->stream('scan-results.pdf');
    }
    

    


}
