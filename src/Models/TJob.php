<?php

namespace Ajtarragona\TJobs\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

abstract class TJob 
{

    protected $steps=[];
    protected $options=[];
    protected $name;
    protected $classname;
    protected $queue = "tjobs-queue";
    

    abstract protected function setup();

    public function __construct($options=[]) {
        $this->options=$options;
        if(isset($options["queue"])) $this->queue=$options["queue"];
        $this->classname=get_class($this);
        $this->name=isset($options["name"])? isset($options["name"]) : Str::slug(Str::snake($this->classname));
        // dd($this);
    }

    public function getOption($name, $default=null){
        return data_get($this->options, $name, $default);
    }

    public function addStep($step, $args=[]){
        if($step instanceof TJobStep) $this->steps[]=$step;
        else if(is_callable($step)) $this->steps[]=new TJobStep($step, $args);

        return $this;
    }


   

    protected function prepareWeights(){
        $usedweight=0;
        $numwithnoweight=0;
        
        // dd($this->steps);
        
        foreach($this->steps as $step){
            $usedweight += $step->weight;
            if($step->weight==0) $numwithnoweight++;
        }
        //si la suma es mayor que 100, devuelvo una excepcion
        if($usedweight>100) throw new Exception("Total Weight exceeds 100%");

        //recoorro los que no tengan peso y les pongo lo que queda
        $stepweight=(100-$usedweight)/$numwithnoweight;
        // dd($stepweight);
        $totalweight=0;

        foreach($this->steps as $i=>$step){
            if($step->weight==0) $this->steps[$i]->weight=$stepweight;

            $totalweight += $this->steps[$i]->weight;
        }
        // dd($this->steps);
        
        if( ceil($totalweight) < 100) throw new Exception("Total weight must be equal to 100%");

    }

    
    public function run(){
        // lo creo en BBDD

        $model=TJobModel::create([
            'queue'=>$this->queue,
            'classname'=>$this->classname,
            'name'=>$this->name,
            'user_id'=>auth()->user()?auth()->user()->id:null,
            'progress'=>0,
            'started_at'=>Date::now()
        ]);
        
        if($this->steps){
            
            

            $this->prepareWeights();
            // dd($this);
            try{
                $progress=0;
                $error=false;
                foreach($this->steps as $i=>$step){
                    $progress = $progress + $step->weight;
                    if($step->run()){
                        $model->update([
                            'progress'=>$progress
                        ]);
                    }else{
                        $error=true;
                        $model->update([
                            'failed'=>true,
                            'finished_at'=>Date::now(),
                            'trace'=>"Error in step ". $i
                        ]);
                        break;
                    }
                    
                }
                if(!$error){
                    $model->update([
                        'progress'=>100,
                        'finished_at'=>Date::now(),
                    ]);
                }
                

            }catch(Exception $e){
                $model->update([
                    'failed'=>true,
                    'finished_at'=>Date::now(),
                    'trace'=>$e->getTraceAsString()
                ]);

                // dd($e);
                // abort(500,$e->getMessage());
                // $this->abort($e->getMessage());
                // echo "\n".json_encode(["progress"=>"100", "message"=>"error"]);
            }
            
            
           
        }
    }

}