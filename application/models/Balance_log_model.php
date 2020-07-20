<?php


class Balance_log_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'balance_log';

    /** @var int  */
    protected $userId;
    /** @var int */
    protected $bankId;
    /** @var int */
    protected $operation;
    /** @var int */
    protected $amount;

    protected $user;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
        return $this->save('userId', $userId);
    }

    /**
     * @return User_model
     */
    public function getUser():User_model
    {
        $this->is_loaded(TRUE);

        if (empty($this->user))
        {
            try {
                $this->user = new User_model($this->getUserId());
            } catch (Exception $exception)
            {
                $this->user = new User_model();
            }
        }
        return $this->user;
    }

    /**
     * @return int
     */
    public function getBankId(): int
    {
        return $this->bankId;
    }

    /**
     * @param int $bankId
     * @return bool
     */
    public function setBankId(int $bankId)
    {
        $this->bankId = $bankId;
        return $this->save('bankId', $bankId);
    }

    /**
     * @return int
     */
    public function getOperation(): int
    {
        return $this->operation;
    }

    /**
     * @param int $operation
     * @return bool
     */
    public function setOperation(int $operation)
    {
        $operation = new Ref_operation_model($operation);
        //
        $this->operation = $operation;
        return $this->save('operation', $operation);
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return bool
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this->save('amount', $amount);
    }

    function __construct($id = NULL)
    {
        parent::__construct();

        $this->set_id($id);
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
        return new static(App::get_ci()->s->get_insert_id());
    }

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }
}
