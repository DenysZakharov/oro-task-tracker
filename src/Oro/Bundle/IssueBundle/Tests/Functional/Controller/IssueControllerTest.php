<?php

namespace Oro\Bundle\IssueBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllersTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('issue_create'));
        $form = $crawler->selectButton('Save and Close')->form();
        $form['issue[code]'] = 'new code';
        $form['issue[summary]'] = 'summary';
        $form['issue[type]'] = 'story';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("NEWCODE", $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testCreateSubissue()
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'subissue_create',
                ['issue' => 'NEWCODE']
            )
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['issue[code]'] = 'new code subissue';
        $form['issue[summary]'] = 'summary';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("NEWCODESUBISSUE", $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testIndex()
    {
        $response = $this->client->requestGrid(
            'issue-grid',
            ['issue-grid[_filter][code][value]' => 'NEWCODE']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $this->assertContains("NEWCODE", $result['code']);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'issue-grid',
            ['issue-grid[_filter][code][value]' => 'NEWCODE']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('issue_update', ['id' => $result['id']])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_issue[code]'] = 'new code updated';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("NEWCODEUPDATED", $crawler->html());
    }

    /**
     * @depends testUpdate
     */
    public function testView()
    {
        $response = $this->client->requestGrid(
            'issue-grid',
            ['issue-grid[_filter][code][value]' => 'NEWCODEUPDATED']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_issue_view', ['id' => $result['id']])
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("NEWCODEUPDATED", $crawler->html());
    }
}
