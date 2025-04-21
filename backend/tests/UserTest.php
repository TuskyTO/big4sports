<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGet_UserList()
    {
        $response = $this->makeHttpRequest('GET', 'http://localhost/big4sports/backend/api_trivia.php');
        $this->assertEquals(200, $response['status']);
    }

    public function testPost_CreateUser()
    {
        $data = [
            'action' => 'register',
            'username' => 'newUser123',
            'password' => 'newPassword123',
        ];
        $response = $this->makeHttpRequest('POST', 'http://localhost/big4sports/backend/api_trivia.php', $data);
        $this->assertEquals(201, $response['status']);
    }

    public function testPost_LoginUser()
    {
        $data = [
            'action' => 'login',
            'username' => 'charlieknapp',
            'password' => 'password25',
        ];
        $response = $this->makeHttpRequest('POST', 'http://localhost/big4sports/backend/api_trivia.php', $data);
        $this->assertEquals(201, $response['status']);
    }

    public function testPost_FailedLogin()
    {
        $data = [
            'action' => 'login',
            'username' => 'nonexistentUser',
            'password' => 'wrongPassword',
        ];
        $response = $this->makeHttpRequest('POST', 'http://localhost/big4sports/backend/api_trivia.php', $data);
        $this->assertEquals(401, $response['status']);
    }

    // Helper function to simulate HTTP request (basic version using file_get_contents)
    private function makeHttpRequest($method, $url, $data = [])
{
    $options = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n",
            'ignore_errors' => true
        ]
    ];

    if (!empty($data)) {
        $options['http']['content'] = json_encode($data);
    }

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500 Internal Server Error';
    preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
    $statusCode = (int)$match[1];

    return [
        'status' => $statusCode,
        'body' => $result
    ];
}

}
