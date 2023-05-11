<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Inertia\Inertia;
use App\Models\Bills;
use Illuminate\Http\Request;
// include ('vendor/autoload.php');
// use Symfony\Component\Panther\Client;
// use Symfony\Component\Panther\Client;



class LatestBillsController extends Controller
{

    public $kansasBaseUrl = 'http://www.kslegislature.org';
    public $kansasHouseBillsUrl = 'http://www.kslegislature.org/li/b2023_24/measures/bills/house/';
    public $kansasSenateBillsUrl = 'http://www.kslegislature.org/li/b2023_24/measures/bills/senate/';
    public $client;
    public $statusString;
    public $bills = [];
    public $body;
    public $billsUrl;

    public function __construct($body='') {

        $this->client = new Client();
        $this->body = $body;
        if($body == 'house') {
            $this->billsUrl = $this->kansasHouseBillsUrl;
        }
        elseif($body == 'senate') {
            $this->billsUrl = $this->kansasSenateBillsUrl;
        }
    }

    /**
     * Scrapes Kansas state bills page and saves records in database
     * This function is called by custom command
     * 
     */
    public function fetchLatestBills() {

        // $bills = [];
        $mainCrawler = $this->client->request('GET', $this->billsUrl);

        $mainCrawler->filter('.module-title')->each(function ($mainNode) {

            $billData = [];
            $billUrl = $this->kansasBaseUrl . $mainNode->extract(['href'])[0];
            $billData['billUrl'] = $billUrl;
            $billData['billDescription'] = $mainNode->text();
            $subCrawler = $this->client->request('GET', $billUrl);
            $billData['title'] = $this->getBillTitle($subCrawler);
            // echo $billData['title'];exit;
            $billData['status'] = $this->getBillStatus($subCrawler);

            $this->bills[] = $billData;
        });

        if(!count($this->bills)) {
            return 'Nothing fetched';
        }
        $saveStatus = $this->saveBills();
        return $saveStatus;
    }

    /**
     * Returns title of bill
     *
     */
    public function getBillTitle($crawler) {

        $title = $crawler->filter('#main > h1')->each(function ($title) use (& $var) {
            return $title->text();
        });
        if(!$title) {
            return 'TITLE NOT FOUND';
        }
        return $title[0];
    }

    /**
     * Returns status of bill
     *
     */
    public function getBillStatus($crawler) {

        $statusString = '';
        $status = [];
        $crawler->filter('#history-tab-1 > tr')->first()->filter('td')->each(function ($node) use (& $statusString){
            $statusString .= $node->text() . '!~!'; // separator
        });
        $statusString = explode('!~!', $statusString);
        if(count($statusString) >= 2) {
            $status['date'] = $statusString[0];
            $status['body'] = $statusString[1];
            $status['detail'] = $statusString[2];
        }

        return $status;
    }

    /**
     * Insert/Update bill information in database
     *
     * @return void
     */
    public function saveBills() {
        $bills = $this->bills;

        $updatedCount = 0;
        $createdCount = 0;

        foreach($bills as $bill) {
            
            $existingBill = Bills::where('url', $bill['billUrl'])->get();
            
            // Update if existing
            if(count($existingBill) && $bill['title']) {
                $existingBill->title = $bill['title'];
                $existingBill->description = $bill['billDescription'];
                $existingBill->body = $this->body;
                $existingBill->status_date = $bill['status']['date'];
                $existingBill->status_body = $bill['status']['body'];
                $existingBill->status_detail = $bill['status']['detail'];
                $existingBill->save();
                $updatedCount++;
            }
            else{

                if($bill['title']) {
                    Bills::create([
                        'title' => $bill['title'],
                        'description' => $bill['billDescription'],
                        'url' => $bill['billUrl'],
                        'body' => $this->body,
                        'status_date' => (isset($bill['status']['date'])) ? $bill['status']['date'] : '',
                        'status_body' => isset($bill['status']['body']) ? $bill['status']['body'] : '',
                        'status_detail' => isset($bill['status']['detail']) ? $bill['status']['detail'] : '',
                    ]);
                    $createdCount++;
                }
            }
        }

        return [
            'updatedCount' => $updatedCount,
            'createdCount' => $createdCount
        ];
    }

    /**
     * Returns house bills information to house page
     *
     */
    public function showHouseBills() {

        return Inertia::render(
            'HouseBills',
            [
                'bills' => Bills::where('body', 'house')->paginate(10),
            ]
        );
    }

    /**
     * Returns senate bills information to senate page
     *
     */
    public function showSenateBills() {

        return Inertia::render(
            'SenateBills',
            [
                'bills' => Bills::where('body', 'senate')->paginate(10),
            ]
        );
    }
}
