<?php

namespace Apsynf\Bundle\ModelManagerBundle;

abstract class DocumentManager {

    protected $em;
    protected $class;
    protected $repository;
    protected $container;

    /**
     * Constructor.
     *
     * @param string   $class
     */
    public function __construct($class) {
        $this->class = $class;
    }

    public function setManager(\Doctrine\ODM\MongoDB\DocumentManager $dm) {
        $this->em = $dm;
        $this->repository = $dm->getRepository($this->class);
        $metadata = $dm->getClassMetadata($this->class);
        $this->class = $metadata->name;
    }

    public function setContainer($container) {
        $this->container = $container;
    }

    public function getContainer() {
        return $this->container;
    }

    public function getDispatcher() {
        return $this->getContainer()->get('event_dispatcher');
    }

    /**
     * Create model object
     *
     * @return BaseModel
     */
    public function create() {
        $class = $this->getClass();
        return new $class;
    }

    /**
     * Persist the model
     *
     * @param $model
     * @param boolean $flush
     * @return BaseModel
     */
    public function persist($model, $flush = true) {
        $this->_persist($model, $flush);
        return $model;
    }

    /**
     * 	This is basic persist function. Child model can overwrite this.
     */
    protected function _persist($model, $flush = true) {
        $this->em->persist($model);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Update the model
     *
     * @param $model
     * @param boolean $flush
     * @return BaseModel
     */
    public function update($model, $flush = true) {
        $this->_update($flush);
        return $model;
    }

    /**
     * 	This is basic update function. Child model can overwrite this.
     */
    protected function _update($flush = true) {
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Delete a model.
     *
     * @param BaseModel $model
     */
    public function delete($model, $flush = true) {
        $this->_delete($model, $flush);
    }

    /**
     * Remove model.
     */
    public function _delete($model, $flush = true) {
        $this->em->remove($model);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Reload the model data.
     */
    public function reload($model) {
        $this->em->refresh($model);
    }

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param $id
     * @return BaseModel
     */
    public function find($id) {
        return $this->em->find($this->class, $id);
    }

    public function isDebug() {
        return $this->container->get('kernel')->isDebug();
    }

    /**
     * Get logged user
     * @return type
     */
    public function getUser() {
        $security = $this->container->get('security.token_storage');
        return $security->getToken()->getUser() ? $security->getToken()->getUser() : null;
    }

}
