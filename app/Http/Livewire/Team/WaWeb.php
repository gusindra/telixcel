<?php

namespace App\Http\Livewire\Team;

use App\Models\WaWeb as ModelsWaWeb;
use Livewire\Component;

class WaWeb extends Component
{
    public $team;
    public $wa_session;
    public $barcode = 0;
    public $ready = false;

    /**
     * mount
     *
     * @return void
     */
    public function mount($team)
    {
        $this->team = $team;
        $this->wa_session = $team->waWeb;
    }

    public function connect(){
        if($this->wa_session && $this->wa_session->status != 0){
            $this->dispatchBrowserEvent('send-session-wa', [
                'session'   => $this->wa_session->session,
                'team'      => $this->team->id,
            ]);
        }else{
            $this->rescan();
        }
    }

    public function rescan(){
        $this->ready = false;
        if($this->wa_session){
            ModelsWaWeb::where("team_id", $this->team->id)->delete();
            return redirect()->to('teams/'.$this->team->id);
        }

        $this->dispatchBrowserEvent('request-session-wa', [
            'team_id'   => $this->team->id,
            'team_name' => $this->team->name,
            'session'   => ''
        ]);
    }

    public function reload(){
        ModelsWaWeb::where("team_id", $this->team->id)->delete();
    }

    public function setBarcode($data){
        $this->barcode = $data;
    }

    public function saveSession($data){
        // dd($data);
        if($data==1){
            $data = [
                "WABrowserId" => '"xr0x0Agxf/9kxx1ZflPq2w=="',
                "WASecretBundle" => '{"key":"j70gIvTDGBKQ30aOz47QkVQoOhIQyVGgWgq715s1OK8=","encKey":"lGCXkcjTJd+dY6shadI1DzEil7tqQUzsPJ61b1nLCXc=","macKey":"j70gIvTDGBKQ30aOz47QkVQoOhIQyVGgWgq715s1"',
                "WAToken1" => '"yo1etO+DdrQX/KofBbm0KTVGiqXb4VFQMGtxRzoS8uI="',
                "WAToken2" => '"1@pY32/zTLg6Y+nN3NGPthDCJr1CQphvCrr3ZYUFIIeDkWKnUTELH4MlfvD/dZgTLic56d1V1dy97iyg=="'
            ];
        }
        // dd($this->wa_session);
        $data = json_encode($data);
        // dd($data);
        if($this->wa_session!=null){
            ModelsWaWeb::where("team_id", $this->team->id)->update([
                'status' => 1,
                'session' => $data //json_encode($data)
            ]);
            $this->emit('saved');
        }else{
            ModelsWaWeb::create([
                'session' => $data, //json_encode($data),
                'team_id' => $this->team->id,
                'status' => 1
            ]);
            // $this->team->waWeb->create([
            //     'wa_browser_id' => $data["WABrowserId"],
            //     'wa_secret_bundle' => $data["WASecretBundle"],
            //     'wa_token1' => $data["WAToken1"],
            //     'wa_token2' => $data["WAToken2"]
            // ]);
            $this->emit('created');
        }
    }

    public function isReady()
    {
        $this->ready = true;
        ModelsWaWeb::where("team_id", $this->team->id)->update([
            'status' => 1
        ]);
        return redirect()->to('teams/'.$this->team->id);
    }

    public function isFail()
    {
        $this->ready = false;
        ModelsWaWeb::where("team_id", $this->team->id)->update([
            'status' => 0
        ]);
    }

    public function render()
    {
        return view('livewire.team.wa-web');
    }
}
