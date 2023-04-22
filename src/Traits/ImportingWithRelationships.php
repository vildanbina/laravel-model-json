<?php

namespace Vildanbina\ModelJson\Traits;

use DB;
use Illuminate\Database\Eloquent\Relations;

/**
 * Trait ImportingWithRelationships
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait ImportingWithRelationships
{
    use HasRelationships;

    /**
     * Imports the specified relationships for the given model.
     *
     * @param Illuminate\Database\Eloquent\Model $model The model to import relationships for
     * @param array $relationships The relationships to import
     * @param array $relations The names of the relationships to import
     * @return void
     */
    private function importRelationships($model, array $relationships, array $relations)
    {
        // Import only the specified related models
        foreach ($relations as $relation) {
            if (isset($relationships[$relation])) {
                $relatedModels = $relationships[$relation];
                $relationType = $model->$relation();

                match (true) {
                    $relationType instanceof Relations\HasOne => $this->handleHasOne($model, $relation, $relatedModels),
                    $relationType instanceof Relations\HasMany => $this->handleHasMany($model, $relation, $relatedModels),
                    $relationType instanceof Relations\HasOneThrough => $this->handleHasOneThrough($model, $relation, $relatedModels),
                    $relationType instanceof Relations\HasManyThrough => $this->handleHasManyThrough($model, $relation, $relatedModels),
                    $relationType instanceof Relations\MorphOne => $this->handleMorphOne($model, $relation, $relatedModels),
                    $relationType instanceof Relations\MorphMany => $this->handleMorphMany($model, $relation, $relatedModels),
                    $relationType instanceof Relations\MorphToMany => $this->handleMorphToMany($model, $relation, $relatedModels),
                    $relationType instanceof Relations\MorphTo => $this->handleMorphTo($model, $relation, $relatedModels),
                    $relationType instanceof Relations\BelongsTo => $this->handleBelongsTo($model, $relation, $relatedModels),
                    $relationType instanceof Relations\BelongsToMany => $this->handleBelongsToMany($model, $relation, $relatedModels),
                };
            }
        }
    }

    /**
     * Handle creating or updating related models in a HasOne relationship.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the HasOne relationship
     * @param array $relatedModelData The data for the related model
     * @return void
     */
    private function handleHasOne($model, $relation, $relatedModelData)
    {
        // Get the instance of the HasOne relation.
        $relationInstance = $model->$relation();

        // Get the related model class.
        $relatedModelClass = get_class($relationInstance->getRelated());

        // Get the foreign key name.
        $foreignKey = $relationInstance->getForeignKeyName();

        // Remove the foreign key from the related model data.
        unset($relatedModelData[$foreignKey]);

        // Get or create the related model.
        $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);
        $relatedModel->fill($relatedModelData);

        // Associate the related model with the parent model.
        $model->$relation()->save($relatedModel);
    }

    /**
     * Handle creating or updating related models in a HasMany relationship.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the HasMany relationship
     * @param array $relatedModels An array of data for the related models
     * @return void
     */
    private function handleHasMany($model, $relation, array $relatedModels)
    {
        foreach ($relatedModels as $relatedModelData) {
            // Check if the related model already exists or create a new one based on the id.
            $relatedModel = $model->$relation()->firstOrNew(['id' => $relatedModelData['id']]);

            // Fill the related model's attributes and save it.
            $relatedModel->fill($relatedModelData);
            $relatedModel->save();
        }
    }

    /**
     * Handle creating or updating related models in a HasOneThrough relationship.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the HasOneThrough relationship
     * @param array $relationships An array of data for the related model and any potential relationships
     * @return void
     */
    private function handleHasOneThrough($model, $relation, array $relationships)
    {
        // Get the HasOneThrough relationship instance and the related model class.
        $relationInstance = $model->$relation();
        $relatedModelClass = get_class($relationInstance->getRelated());

        // Get or create the related model based on the id.
        $relatedModel = $relatedModelClass::firstOrNew([$relationInstance->getRelated()->getKeyName() => $relationships[$relationInstance->getRelated()->getKeyName()]]);
        $relatedModel->fill($relationships);

        // Get the parent model and the through model instance and class.
        $parentModel = $relationInstance->getParent();
        $throughModelInstance = $relationInstance->getThroughParent();
        $throughModelClass = get_class($throughModelInstance);

        // Get or create the through model based on the foreign key.
        $throughModel = $throughModelClass::firstOrNew([$relationInstance->getForeignKeyName() => $relationships[$relationInstance->getForeignKeyName()]]);

        // Loop through any potential relationships between the related model and other models.
        foreach (array_filter($relationships, 'is_array') as $potentialRelation => $relationData) {
            if (
                method_exists($relatedModel, $potentialRelation) &&
                $relatedModel->$potentialRelation()->getForeignKeyName() == $relationInstance->getForeignKeyName()
            ) {
                // If a relationship exists, fill the through model with the relationship data and save it.
                $throughModel->fill($relationships[$potentialRelation])->save();
                $relatedModel[$relatedModel->$potentialRelation()->getForeignKeyName()] = $throughModel->getKey();
                break;
            }
        }

        // Save the related model.
        $relatedModel->save();
    }

    /**
     * Handle creating or updating related models in a HasManyThrough relationship.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the HasManyThrough relationship
     * @param array $relatedModels An array of data for the related models
     * @return void
     */
    private function handleHasManyThrough($model, $relation, array $relatedModels)
    {
        // Loop through the related models and handle each one with the `handleHasOneThrough()` method.
        foreach ($relatedModels as $relatedModelData) {
            $this->handleHasOneThrough($model, $relation, $relatedModelData);
        }
    }

    /**
     * Handle creating or updating a related model in a MorphOne relationship and associating it with the parent model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the MorphOne relationship
     * @param array $relatedModelData The data for the related model
     * @return void
     */
    private function handleMorphOne($model, $relation, $relatedModelData)
    {
        // Get the MorphOne relationship instance and the related model class.
        $relationInstance = $model->$relation();
        $relatedModelClass = get_class($relationInstance->getRelated());

        // Get or create the related model.
        $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);
        $relatedModel->fill($relatedModelData);
        $relatedModel->save();

        // Associate the related model with the parent model by setting its morph type and foreign key values and saving it.
        $relatedModel->{$relationInstance->getMorphType()} = get_class($model);
        $relatedModel->{$relationInstance->getForeignKeyName()} = $model->id;
        $relatedModel->save();
    }

    /**
     * Handle creating or updating related models in a MorphMany relationship by calling the handleMorphOne() method.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the MorphMany relationship
     * @param array $relatedModels An array of data for the related models
     * @return void
     */
    private function handleMorphMany($model, $relation, array $relatedModels)
    {
        // Get the MorphMany relationship instance and the related model class, and call the handleMorphOne() method for each related model.
        $relationInstance = $model->$relation();
        $relatedModelClass = get_class($relationInstance->getRelated());

        foreach ($relatedModels as $relatedModelData) {
            $this->handleMorphOne($model, $relation, $relatedModelData);
        }
    }

    /**
     * Handle creating or updating related models in a MorphToMany relationship and updating the pivot table as necessary.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the MorphToMany relationship
     * @param array $relatedModels An array of data for the related models
     * @return void
     */
    private function handleMorphToMany($model, $relation, array $relatedModels)
    {
        // Get the MorphToMany relationship instance, the related model class, the pivot table name, and the pivot data columns.
        $relationInstance = $model->$relation();
        $relatedModelClass = get_class($relationInstance->getRelated());
        $pivotTable = $relationInstance->getTable();
        $pivotData = $relationInstance->getPivotColumns();

        // Loop through the related models, creating or updating each related model and updating the pivot table as necessary.
        foreach ($relatedModels as $relatedModelData) {
            // Get or create the related model.
            $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);
            $relatedModel->fill($relatedModelData);
            $relatedModel->save();

            // Prepare pivot data for the related model.
            $pivotAttributes = array_intersect_key($relatedModelData['pivot'], array_flip($pivotData));
            $pivotAttributes[$relationInstance->getMorphType()] = get_class($model);
            $pivotAttributes[$relationInstance->getForeignPivotKeyName()] = $model->id;
            $pivotAttributes[$relationInstance->getRelatedPivotKeyName()] = $relatedModel->id;

            // Insert or update pivot data for the related model.
            DB::table($pivotTable)->updateOrInsert(
                [
                    $relationInstance->getMorphType() => $pivotAttributes[$relationInstance->getMorphType()],
                    $relationInstance->getForeignPivotKeyName() => $pivotAttributes[$relationInstance->getForeignPivotKeyName()],
                    $relationInstance->getRelatedPivotKeyName() => $pivotAttributes[$relationInstance->getRelatedPivotKeyName()],
                ],
                $pivotAttributes
            );
        }
    }

    /**
     * Handle creating or updating a related model in a MorphTo relationship and associating it with the parent model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the MorphTo relationship
     * @param array $relatedModelData An array of data for the related model
     * @return void
     */
    private function handleMorphTo($model, $relation, $relatedModelData)
    {
        // Get the instance of the MorphTo relation.
        $relationInstance = $model->$relation();

        // Get or create the related model.
        $relatedModelClass = get_class($relationInstance->getRelated());
        $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);
        $relatedModel->fill($relatedModelData);
        $relatedModel->save();

        // Associate the related model with the parent model.
        $model->$relation()->associate($relatedModel);
        $model->save();
    }

    /**
     * Handle creating or updating a related model in a BelongsTo relationship and associating it with the parent model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the BelongsTo relationship
     * @param array $relatedModelData An array of data for the related model
     * @return void
     */
    private function handleBelongsTo($model, $relation, $relatedModelData)
    {
        // Get the related model class and get or create the related model.
        $relatedModelClass = get_class($model->$relation()->getRelated());
        $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);
        $relatedModel->fill($relatedModelData);
        $relatedModel->save();

        // Associate the related model with the parent model.
        $model->$relation()->associate($relatedModel);
        $model->save();
    }

    /**
     * Handle creating or updating related models in a BelongsToMany relationship and syncing them with the parent model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The parent model
     * @param string $relation The name of the BelongsToMany relationship
     * @param array $relatedModels An array of data for the related models
     * @return void
     */
    private function handleBelongsToMany($model, $relation, array $relatedModels)
    {
        // Create an array to hold the related model IDs to sync.
        $syncData = [];

        // Loop through the related models, creating or updating them as necessary, and adding their IDs to the sync data array.
        foreach ($relatedModels as $relatedModelData) {
            $relatedModelClass = get_class($model->$relation()->getRelated());
            $relatedModel = $relatedModelClass::firstOrNew(['id' => $relatedModelData['id']]);

            $relatedModel->fill($relatedModelData);
            $relatedModel->save();

            $syncData[] = $relatedModel->id;
        }

        // Sync the related model IDs with the parent model using the `sync()` method on the relationship instance.
        $model->$relation()->sync($syncData);
    }
}
