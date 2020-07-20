<?php


class Ref_operation_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'ref_operation';

    const OPERATION_ADD_BALANCE     = 1;
    const OPERATION_ADD_LIKE        = 2;
    const OPERATION_BUY_BOOSTERPACK = 3;
    const OPERATION_ADD_BANK        = 4;

    /** @var int  */
    protected $id;
    /** @var string */
    protected $name;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this->save('id', $id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this->save('name', $name);
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
