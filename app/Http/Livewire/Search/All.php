<?php

namespace App\Http\Livewire\Search;

use Illuminate\Support\Str;
use Livewire\Component;

class All extends Component
{
    public $modalActionVisible = false;
    public $keyword;
    public $results = [];

    private $models = [
        'Order',
        'Quotation',
        'Contract',
        'CommerceItem',
        'Project',
        'Billing'
    ];

    private $urls = [
        'Order' => '/order/:id',
        'Quotation' => '/commercial/quotation/:id',
        'Contract' => '/commercial/contract/:id',
        'CommerceItem' => '/commercial/item/:id',
        'Project' => '/project/:id',
        'Billing' => '/commercial/:id/invoice/print'
    ];

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function search()
    {
        $this->results = [];
        sleep(5);
        foreach($this->models as $model){
            $modelClass = 'App\Models\\'.$model;
            $query = $modelClass::query();
            $fields = $modelClass::$searchable;
            foreach($fields as $field){
                $query->orWhere($field, 'LIKE', '%'.$this->keyword.'%');
            }

            $results = $query->take(10)->get();

            foreach($results as $result){
                $parsedResult['model']  = $model;
                $parsedResult['fields'] = $fields;
                $formattedField    = [];
                $parsedResult   = $result->only($fields);

                foreach($fields as $field){
                    $formattedField[$field]    = Str::title(str_replace('_', ' ', $field));
                }
                $parsedResult['fields_formatted']   = $formattedField;

                $parsedResult['url'] = url(str_replace(':id', $model=='Billing'?$result->order_id:$result->id, $this->urls[$model]));

                $this->results[$model][] = $parsedResult;
            }
        }
    }

    private function readResult()
    {
        return $this->results;
    }

    public function render()
    {
        return view('livewire.search.all', [
            'results' => $this->readResult()
        ]);
    }
}
