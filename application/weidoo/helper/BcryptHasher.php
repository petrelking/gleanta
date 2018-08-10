<?php

// +----------------------------------------------------------------------
// | 密码加密，参考的是laravel
// +----------------------------------------------------------------------
// | Copyright (c)
// +----------------------------------------------------------------------
// | Author:wandehua
// +----------------------------------------------------------------------

namespace app\weidoo\helper;

class BcryptHasher {

    protected static $instance;

    /**
     * Default crypt cost factor.
     *
     * @var int
     */
    protected static $rounds = 10;

    private function __construct(){}

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Tree
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function make($value, array $options = [])
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : self::$rounds;

        $hash = password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);

        if ($hash === false) {
            throw new \Exception('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function check($value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function needsRehash($hashedValue, array $options = [])
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : self::$rounds;

        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * Set the default password work factor.
     *
     * @param  int  $rounds
     * @return $this
     */
    public static function setRounds($rounds)
    {
        self::$rounds = (int) $rounds;
    }

}
