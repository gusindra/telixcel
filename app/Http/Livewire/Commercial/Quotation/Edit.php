<?php

namespace App\Http\Livewire\Commercial\Quotation;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $quote;
    public $quoteNo;
    public $company;
    public $price;
    public $discount;
    public $model;
    public $model_id;
    public $name;
    public $date;
    public $valid_day;
    public $status;
    public $type;
    public $terms;
    public $created_by;
    public $created_role;
    public $addressed;
    public $addressed_name;
    public $addressed_role;
    public $addressed_company;
    public $description;
    public $source;
    public $source_id;
    public $client_id;

    public function mount($code, $source=null, $source_id=null)
    {
        $this->quote = Quotation::find($code);
        $this->name = $this->quote->title;
        $this->date = $this->quote->date;
        $this->valid_day = $this->quote->valid_day;
        $this->status = $this->quote->status;
        $this->type = $this->quote->type;
        $this->quoteNo = $this->quote->quote_no;
        $this->terms = $this->quote->terms;
        $this->price = $this->quote->price;
        $this->discount = $this->quote->discount;
        $this->model = $this->quote->model;
        $this->model_id = $this->quote->model_id;
        $this->addressed_company = $this->quote->addressed_company;
        $this->description = $this->quote->description;
        $this->created_by = $this->quote->created_by == '' ? auth()->user()->name : $this->quote->created_by;
        $this->created_role = $this->quote->created_role == '' ? '' : $this->quote->created_role;
        $this->addressed_name = $this->quote->addressed_name;
        $this->addressed_role = $this->quote->addressed_role;
        $this->client_id = $this->quote->client_id;

        // Load existing client into $addressed so the form shows current selection
        if ($this->quote->client_id) {
            $this->addressed = Client::find($this->quote->client_id);
        }

        if($source && $source_id){
            $this->source = $source;
            $this->source_id = $source_id;
        }
    }

    public function rules()
    {
        return [
            'quoteNo' => 'required'
        ];
    }

    public function modelData()
    {
        $data = [
            'title'             => $this->name,
            'status'            => $this->status,
            'quote_no'          => $this->quoteNo,
            'date'              => $this->date,
            'valid_day'         => $this->valid_day,
            'terms'             => $this->terms,
            'model'             => $this->model,
            'model_id'          => $this->model_id,
            'addressed_company' => $this->addressed_company,
            'description'       => $this->description,
            'created_by'        => $this->created_by,
            'created_role'      => $this->created_role,
            'addressed_name'    => $this->addressed_name,
            'addressed_role'    => $this->addressed_role,
        ];

        if ($this->client_id) {
            $data['client_id'] = $this->client_id;
        }

        return $data;
    }

    public function update($id)
    {
        $this->validate();
        Quotation::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    /** Called when the client dropdown in Customer Information changes. */
    public function onClientChange($clientId)
    {
        $this->client_id = $clientId ?: null;
        $this->addressed = $clientId ? Client::find($clientId) : null;
    }

    /** Clients available for project-based quotations (from project_client pivot). */
    public function readProjectClients()
    {
        $project = optional($this->quote)->project;
        if (! $project) {
            return collect();
        }
        // Try project's linked clients first; fallback to all clients in the team
        $clients = $project->clients ?? collect();
        if ($clients->isEmpty()) {
            return Client::where('user_id', auth()->user()->currentTeam->user_id ?? 0)->get();
        }
        return $clients;
    }

    public function onChangeModelId()
    {
        if($this->model_id!=0){
            if($this->type=='project'){
                $this->addressed = Project::find($this->model_id);
                $this->model = 'PROJECT';
            }else{
                $this->addressed = Client::find($this->model_id);
                $this->model = 'CLIENT';
            }
            $this->addressed_company = $this->addressed->name;
        }else{
            $this->model = NULL;
            $this->model_id = NULL;
            $this->addressed_company = NULL;
            $this->addressed = '';
        }
    }

    public function generateNo(){
        $code = '';
        if($this->type=='PROJECT'){
            $code = $this->quote->project->company->code;
        }elseif($this->type=='COMPANY'){
            $code = $this->quote->company->code;
        }
        $this->quoteNo = $code.date('Ymd').$this->quote->id;
    }


    /**
     * The read function.
     *
     * @return void
     */
    public function readModelSelection()
    {
        if($this->type=='project'){
            $data = Project::where('team_id', auth()->user()->currentTeam->team_id)->pluck('name', 'id');
        }else{
            $data = Client::where('user_id', auth()->user()->currentTeam->user_id)->pluck('name', 'id');
        }
        return $data;
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function readSourceSelection()
    {
        // Embedded inside a specific project -> lock the Source ID to that project.
        if($this->source=='project'){
           return Project::where('id', $this->model_id)->pluck('name', 'id');
        }

        if($this->model=='PROJECT'){
            // List all projects in the team so the user can pick one.
            return Project::where('team_id', auth()->user()->currentTeam->team_id)->pluck('name', 'id');
        }elseif($this->model=='COMPANY'){
            // Use the shared helper so ALL companies from Company Settings appear
            // (same list as the "create project" company picker).
            return get_my_companies()->pluck('name', 'id');
        }elseif($this->model=='CLIENT'){
            return Client::where('user_id', auth()->user()->currentTeam->user_id)->pluck('name', 'id');
        }
        return collect();
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readClient()
    {
        return $this->addressed;
    }

    public function render()
    {
        return view('livewire.commercial.quotation.edit', [
            'source_list'     => $this->readSourceSelection(),
            'model_list'      => $this->readModelSelection(),
            'client'          => $this->readClient(),
            'project_clients' => $this->readProjectClients(),
        ]);
    }
}
