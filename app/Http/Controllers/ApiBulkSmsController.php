<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessSmsApi;
use App\Jobs\ProcessSmsStatus;
use App\Models\ApiCredential;

class ApiBulkSmsController extends Controller
{
    public function post(Request $request)
    {
        //get the request & validate parameters
        $fields = $request->validate([
            'type' => 'required|numeric',
            'to' => 'required|string',
            'from' => 'required|alpha_num',
            'text' => 'required|string',
            'servid' => 'required|string',
            'title' => 'required|string',
            'detail' => 'string',
        ]);

        try{
            $userCredention = ApiCredential::where("user_id", auth()->user()->id)->where("client", "api_sms_mk")->where("is_enabled", 1)->first();
            ProcessSmsApi::dispatch($request->all(), $userCredention);
        }catch(\Exception $e){
            return response()->json([
                'Msg' => "Failed",
                'Status' => 400
            ]);
        }
        // show result on progress
        return response()->json([
            'Msg' => "Successful",
            'Status' => 200
        ]);
    }

    /**
     * status
     *
     * @param  mixed $request->msgid
     * @param  mixed $request->msisdn
     * @param  mixed $request->status
     * @return void
     */
    public function status(Request $request)
    {
        ProcessSmsStatus::dispatch($request->all());

        return response()->json([
            'Msg' => "Process to update",
            'Status' => 200
        ]);
    }

    public function ginee()
    {
        return response()->json([
            'code' => "SUCCESS",
            'message' => "OK",
            'data' => [
                "page" => 0,
                "size" => 20,
                "total" => 318,
                "content" => [
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A2F7",
                        "inventoryName" => "Xiaobailong1",
                        "inventorySku" => "Navy1234",
                        "purchasePrice" =>  9000,
                        "stock" =>  [
                          "availableStock" =>  25,
                          "warehouseStock" =>  999999,
                          "promotionStock" =>  null,
                          "spareStock" =>  10,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "Xiaobailong",
                        "length" =>  110,
                        "width" =>  120,
                        "height" =>  130,
                        "weight" =>  140,
                        "skuList" =>  [
                          "Navy1234"
                        ],
                        "updateAt" => "2022-02-10T16:24:36Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN61B9C9C359080100012C803C",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "122211"
                        ],
                        "updateAt" => "2021-12-16T00:56:03Z",
                        "createAt" => "2021-12-16T00:56:03Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A7DD6B2F0700013CAFE6",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "122211"
                        ],
                        "updateAt" => "2021-12-15T22:31:25Z",
                        "createAt" => "2021-12-15T22:31:25Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A7C759080100012C8037",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "122211"
                        ],
                        "updateAt" => "2021-12-15T22:31:03Z",
                        "createAt" => "2021-12-15T22:31:03Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A7B66B2F0700013CAFE0",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:30:46Z",
                        "createAt" => "2021-12-15T22:30:46Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A79D59080100012C8031",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:30:21Z",
                        "createAt" => "2021-12-15T22:30:21Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A78159080100012C802B",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:29:53Z",
                        "createAt" => "2021-12-15T22:29:53Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A74D6B2F0700013CAFDA",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:29:01Z",
                        "createAt" => "2021-12-15T22:29:01Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A7286B2F0700013CAFD4",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:28:24Z",
                        "createAt" => "2021-12-15T22:28:24Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A7126B2F0700013CAFCE",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:28:02Z",
                        "createAt" => "2021-12-15T22:28:02Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A6E46B2F0700013CAFC8",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:27:16Z",
                        "createAt" => "2021-12-15T22:27:16Z"
                    ],
                    [
                        "inventoryId" => "IN61B9A0BE6B2F0700013CAFC2",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "Minyak Zaitun"
                        ],
                        "updateAt" => "2021-12-15T22:01:02Z",
                        "createAt" => "2021-12-15T22:01:02Z"
                    ],
                    [
                        "inventoryId" => "IN61B99F456B2F0700013CAFBC",
                        "inventoryName" => "Minyak Zaitun",
                        "inventorySku" => "122211",
                        "purchasePrice" =>  40000,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  10,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" => "11505031",
                        "length" =>  5,
                        "width" =>  5,
                        "height" =>  15,
                        "weight" =>  10000,
                        "skuList" =>  [
                          "test"
                        ],
                        "updateAt" => "2021-12-15T21:54:45Z",
                        "createAt" => "2021-12-15T21:54:45Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A38B",
                        "inventoryName" => "hello1Test Create Product 12181605 null",
                        "inventorySku" => "12181605",
                        "purchasePrice" =>  964.91,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  1,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  100,
                        "skuList" =>  [
                          "12181605"
                        ],
                        "updateAt" => "2021-06-21T00:59:12Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A30C",
                        "inventoryName" => "National Museum Batavia 467Standard Adm",
                        "inventorySku" => "TKPPV-195-467",
                        "purchasePrice" =>  751.71,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  9,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  1,
                        "skuList" =>  [
                          "TKPPV-195-467"
                        ],
                        "updateAt" => "2021-06-21T00:58:54Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A288",
                        "inventoryName" => "insert 119 null",
                        "inventorySku" => "1201072377",
                        "purchasePrice" =>  967.74,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  9999,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  1,
                        "skuList" =>  [
                          "1201072377"
                        ],
                        "updateAt" => "2021-06-19T23:07:13Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65252FAFF0001A6A27B",
                        "inventoryName" => "NEW FOR ALL CHANNEL 0311 Ungu/24",
                        "inventorySku" => "tk777",
                        "purchasePrice" =>  244.44,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  124,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  1,
                        "skuList" =>  [
                          "tk777"
                        ],
                        "updateAt" => "2021-06-19T23:07:12Z",
                        "createAt" => "2021-03-24T15:39:14Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A2E7",
                        "inventoryName" => "Bandung Zoo XXL",
                        "inventorySku" => "TKPPV-1801-64tttt",
                        "purchasePrice" =>  491.07,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  8,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  1,
                        "skuList" =>  [
                          "TKPPV-1801-64tttt"
                        ],
                        "updateAt" => "2021-06-19T23:07:12Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A371",
                        "inventoryName" => "helloTest Create Product 12181826 null",
                        "inventorySku" => "12181824",
                        "purchasePrice" =>  942.86,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  1,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  100,
                        "skuList" =>  [
                          "12181824"
                        ],
                        "updateAt" => "2021-06-19T23:07:12Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ],
                    [
                        "inventoryId" => "IN605AA65352FAFF0001A6A37E",
                        "inventoryName" => "hello1Test Create Product 12181611 Putih/M",
                        "inventorySku" => "12181611",
                        "purchasePrice" =>  837.56,
                        "stock" =>  [
                          "availableStock" =>  null,
                          "warehouseStock" =>  308,
                          "promotionStock" =>  null,
                          "spareStock" =>  0,
                          "safetyStock" =>  0,
                          "lockedStock" =>  0
                        ],
                        "barcode" =>  null,
                        "length" =>  0,
                        "width" =>  0,
                        "height" =>  0,
                        "weight" =>  100,
                        "skuList" =>  [
                          "12181611"
                        ],
                        "updateAt" => "2021-06-19T23:07:12Z",
                        "createAt" => "2021-03-24T15:39:15Z"
                    ]
                ]
            ]
        ]);
    }
}
