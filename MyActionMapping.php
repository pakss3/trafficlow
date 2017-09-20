<?php
namespace ApiAi;



class MyActionMapping extends ActionMapping
{
    /**
     * @inheritdoc
     */
    public function action($sessionId, $action, $parameters, $contexts)
    {
        return call_user_func_array(array($this, $action), array($sessionId, $parameters, $contexts));
    }

    /**
     * @inheritdoc
     */
    public function speech($sessionId, $speech, $contexts)
    {
        echo $speech;
    }

    /**
     * @inheritdoc
     */
    public function error($sessionId, $error)
    {
        echo $error;
    }
}