<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MiddlewareTest extends TestCase
{
    use DatabaseMigrations;

    protected $postData;

    public function setUp()
    {
        parent::setUp();
        
        $this->postData = json_decode(file_get_contents(__DIR__ . '/examples/register_post.json'), true);
        //var_dump($this->postData);
    }

    /*
    public function testContentTypeCheck()
    {
        $this->post('/api/register', [])
             ->seeJsonEquals([
                'message' => 'Content-Type is unmatched',
                'errors' => [['code' => 'unmatched_content_type']]
               ])
             ->assertResponseStatus(400);
    }
    */

    public function testApiKeyCheck()
    {
        $this->json('post', '/api/register', $this->postData, ['Content-Type'=> 'application/json'])
             ->seeJsonEquals([
                'message' => 'API key is not validated',
                'errors' => [['code' => 'incorrect_api_key']],
               ])
             ->assertResponseStatus(401);
    }

    public function testPassMiddleware()
    {
        $apiKey = '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
        $headerArray = [
            'X-VMS-API-Key' => $apiKey
        ];

        factory(App\ApiKey::class)->create([
            'api_key' => $apiKey
        ]);

        $this->json('post', '/api/register', $this->postData, $headerArray)
             ->assertResponseStatus(200);
    }
}
