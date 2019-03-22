<?php

namespace App\Search\Engines;

use Elasticsearch\Client;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class ElasticSearchEngine extends Engine
{
    protected $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
     public function update($models){
         //take collection of models
         $models->each(function ($model) {
            $params =[
                "index" => $model->searchableAs(),  // ex. users
                "type" => $model->searchableAs(),
                "id" => $model->id,
                "body" => $model->toSearchableArray()
            ];

            $this->client->index($params);
         });
     }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
     public function delete($models){
         //delete from index
     }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return mixed
     */
     public function search(Builder $builder){
        //don't  return result
         $params = [
             "index" => $builder->model->searchableAs(),  // ex. users
             "type" => $builder->model->searchableAs(),
             "body" => [
                 "query" => [
                     "multi_match"=> [
                         "query"=> $builder->query,
                         "fields"=> ["name", "username", "email"],
                         "type" => "phrase_prefix"
                     ]
                 ]
             ]
         ];

         return $this->client->search($params);
     }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
     public function paginate(Builder $builder, $perPage, $page){}

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
     public function mapIds($results){}

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
     public function map(Builder $builder, $results, $model){
         // return result from elasticsearch
         if(count($hits = collect(array_get($results, 'hits.hits'))) === 0) {
            return $model->newCollection();
         }

         return $model->getScoutModelsByIds(
             $builder,
             $hits->pluck('_id')->values()->all()
         );
     }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
     public function getTotalCount($results){}

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
     public function flush($model){}
}
