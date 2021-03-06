<?php

namespace FSC\HateoasBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class ControllerTest extends TestCase
{
    public function testGetPostXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/posts/2?_format=xml');

        $response = $client->getResponse(); /**  */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<post id="2">
  <title><![CDATA[How to create awesome symfony2 application]]></title>
  <link rel="self" href="http://localhost/api/posts/2"/>
</post>

XML
        , $response->getContent());
    }

    public function testGetUserPostsXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/users/42/posts?_format=xml');

        $response = $client->getResponse(); /**  */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" total="2">
  <link rel="self" href="http://localhost/api/users/42/posts?limit=10&amp;page=1"/>
  <link rel="first" href="http://localhost/api/users/42/posts?limit=10&amp;page=1"/>
  <link rel="last" href="http://localhost/api/users/42/posts?limit=10&amp;page=1"/>
  <post id="2">
    <title><![CDATA[How to create awesome symfony2 application]]></title>
    <link rel="self" href="http://localhost/api/posts/2"/>
  </post>
  <post id="1">
    <title><![CDATA[Welcome on the blog!]]></title>
    <link rel="self" href="http://localhost/api/posts/1"/>
  </post>
</collection>

XML
        , $response->getContent());
    }

    public function testGetUserPostsConfig1Xml()
    {
        $client = $this->createClient(array('environment' => 'test1'));
        $client->request('GET', '/api/users/42/posts?_format=xml');

        $response = $client->getResponse(); /**  */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" total="2">
  <link rel="self" href="http://localhost/api/users/42/posts?pagination%5Blimit%5D=10&amp;pagination%5Bpage%5D=1"/>
  <link rel="first" href="http://localhost/api/users/42/posts?pagination%5Blimit%5D=10&amp;pagination%5Bpage%5D=1"/>
  <link rel="last" href="http://localhost/api/users/42/posts?pagination%5Blimit%5D=10&amp;pagination%5Bpage%5D=1"/>
  <post id="2">
    <title><![CDATA[How to create awesome symfony2 application]]></title>
    <link rel="self" href="http://localhost/api/posts/2"/>
  </post>
  <post id="1">
    <title><![CDATA[Welcome on the blog!]]></title>
    <link rel="self" href="http://localhost/api/posts/1"/>
  </post>
</collection>

XML
            , $response->getContent());
    }

    public function testGetMixedElementNamesXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/mixed?_format=xml');

        $response = $client->getResponse(); /**  */

        $this->assertEquals(200, $response->getStatusCode());

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadXML($response->getContent());
        $xpath = new \DOMXPath($document);

        $nodeList = $xpath->query('/*/post');
        $this->assertEquals(2, $nodeList->length);

        $nodeList = $xpath->query('/*/user');
        $this->assertEquals(1, $nodeList->length);

        $nodeList = $xpath->query('/*/*');
        $this->assertEquals(3, $nodeList->length);
    }

    public function testGetCreatePostFormXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/posts/create?_format=xml');

        $response = $client->getResponse(); /** @var $response Response */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<form method="POST" action="http://localhost/api/posts">
  <link rel="self" href="http://localhost/api/posts/create"/>
  <input type="text" name="post[title]" required="required"/>
</form>

XML
        , $response->getContent());
    }

    public function testListPostsXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/posts?_format=xml');

        $response = $client->getResponse(); /** @var $response Response */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<posts page="1" limit="10" total="3">
  <link rel="self" href="http://localhost/api/posts?limit=10&amp;page=1"/>
  <link rel="first" href="http://localhost/api/posts?limit=10&amp;page=1"/>
  <link rel="last" href="http://localhost/api/posts?limit=10&amp;page=1"/>
  <post id="1">
    <title><![CDATA[Welcome on the blog!]]></title>
    <link rel="self" href="http://localhost/api/posts/1"/>
  </post>
  <post id="2">
    <title><![CDATA[How to create awesome symfony2 application]]></title>
    <link rel="self" href="http://localhost/api/posts/2"/>
  </post>
  <post id="3">
    <title><![CDATA[]]></title>
    <link rel="self" href="http://localhost/api/posts/3"/>
  </post>
  <link rel="create" href="http://localhost/api/posts/create"/>
  <form rel="create" method="POST" action="http://localhost/api/posts">
    <link rel="self" href="http://localhost/api/posts/create"/>
    <input type="text" name="post[title]" required="required"/>
  </form>
</posts>

XML
            , $response->getContent());
    }

    public function testRootControllerXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/?_format=xml');

        $response = $client->getResponse(); /** @var $response Response */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <link rel="users" href="http://localhost/api/users"/>
  <link rel="posts" href="http://localhost/api/posts"/>
</root>

XML
            , $response->getContent());
    }

    public function testRootRuntimeMetadataControllerXml()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/?_format=xml&user_id=1');

        $response = $client->getResponse(); /** @var $response Response */

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <link rel="users" href="http://localhost/api/users"/>
  <link rel="posts" href="http://localhost/api/posts"/>
  <link rel="me" href="http://localhost/api/users/1"/>
</root>

XML
            , $response->getContent());
    }

    public function testGetPostJsonHal()
    {
        $client = $this->createClient(array('environment' => 'hal'));
        $client->request('GET', '/api/posts/2?_format=json');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $expectedJson = <<<JSON
{
    "id":2,
    "title":"How to create awesome symfony2 application",
    "_links":[
        {"rel":"self","href":"http:\/\/localhost\/api\/posts\/2"}
    ]
}
JSON;

        $this->assertEquals($this->removeJsonIndentation($expectedJson), $response->getContent());
    }

    public function testGetRelationsJsonHal()
    {
        $client = $this->createClient(array('environment' => 'hal'));
        $client->request('GET', '/api/mixed?_format=json');

        $response = $client->getResponse();

        $expectedJson = <<<JSON
[
    {
        "id":1,
        "title":"Welcome on the blog!",
        "_links":[
            {"rel":"self","href":"http:\/\/localhost\/api\/posts\/1"}
        ]
    },
    {
        "id":2,
        "title":"How to create awesome symfony2 application",
        "_links":[
            {"rel":"self","href":"http:\/\/localhost\/api\/posts\/2"}
        ]
    },
    {
        "id":1,
        "first_name":"Adrien",
        "last_name":"Brault",
        "_links":[
            {"rel":"self","href":"http:\/\/localhost\/api\/users\/1"},
            {"rel":"alternate","href":"http:\/\/localhost\/profile\/1"},
            {"rel":"users","href":"http:\/\/localhost\/api\/users"},
            {"rel":"last-post","href":"http:\/\/localhost\/api\/users\/1\/last-post"},
            {"rel":"posts","href":"http:\/\/localhost\/api\/users\/1\/posts"}
        ],
        "_embedded":{
            "last-post":{
                "id":2,
                "title":"How to create awesome symfony2 application",
                "_links":[
                    {"rel":"self","href":"http:\/\/localhost\/api\/posts\/2"}
                ]
            },
            "posts":{
                "page":1,
                "limit":1,
                "total":2,
                "results":[
                    {
                        "id":2,
                        "title":"How to create awesome symfony2 application",
                        "_links":[
                            {"rel":"self","href":"http:\/\/localhost\/api\/posts\/2"}
                        ]
                    }
                ],
                "_links":[
                    {"rel":"self","href":"http:\/\/localhost\/api\/users\/1\/posts?limit=1&page=1"},
                    {"rel":"first","href":"http:\/\/localhost\/api\/users\/1\/posts?limit=1&page=1"},
                    {"rel":"last","href":"http:\/\/localhost\/api\/users\/1\/posts?limit=1&page=2"},
                    {"rel":"next","href":"http:\/\/localhost\/api\/users\/1\/posts?limit=1&page=2"}
                ]
            }
        }
    }
]
JSON;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->removeJsonIndentation($expectedJson), $response->getContent());
    }

    protected function removeJsonIndentation($json)
    {
        return preg_replace('/(\n)(?:    )*/', '', $json);
    }
}
