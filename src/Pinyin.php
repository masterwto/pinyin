<?php

/*
 * Created Date: Friday December 16th 2016
 * Author: Pangxiaobo
 * Last Modified: Friday December 16th 2016 4:05:44 pm
 * Modified By: the developer formerly known as Pangxiaobo at <10846295@qq.com>
 * Copyright (c) 2016 Pangxiaobo
 */

namespace Xiaobopang\Pinyin;

class Pinyin
{
    // utf-8 中国汉字集合
    private $chineseCharacters;

    // 编码
    private $charset = 'utf-8';

    public function __construct()
    {
        if (empty($this->chineseCharacters)) {
            $this->chineseCharacters = file_get_contents(__DIR__.'/../data/ChineseCharacters.dat');
        }
    }

    /**
     * 转成带有声调的汉语拼音
     * @param $input_char String  需要转换的汉字
     * @param $delimiter  String   转换之后拼音之间分隔符
     * @param $outside_ignore  Boolean     是否忽略非汉字内容
     */
    public function transformWithTone($input_char, $delimiter = ' ', $outside_ignore = false)
    {
        $input_len = mb_strlen($input_char, $this->charset);
        $output_char = '';
        for ($i = 0; $i < $input_len; $i++) {
            $word = mb_substr($input_char, $i, 1, $this->charset);
            $matches = [];
            if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $word) && preg_match('/\,'.preg_quote($word).'(.*?)\,/', $this->chineseCharacters, $matches)) {
                $output_char .= $matches[1].$delimiter;
            } elseif (!$outside_ignore) {
                $output_char .= $word;
            }
        }

        return $output_char;
    }

    /**
     * 转成带无声调的汉语拼音
     * @param $input_char String  需要转换的汉字
     * @param $delimiter  String   转换之后拼音之间分隔符
     * @param $outside_ignore  Boolean     是否忽略非汉字内容
     */
    public function transformWithoutTone($input_char, $delimiter = '', $outside_ignore = true)
    {
        $char_with_tone = $this->transformWithTone($input_char, $delimiter, $outside_ignore);

        $char_without_tone = str_replace(
            ['ā', 'á', 'ǎ', 'à', 'ō', 'ó', 'ǒ', 'ò', 'ē', 'é', 'ě', 'è', 'ī', 'í', 'ǐ', 'ì', 'ū', 'ú', 'ǔ', 'ù', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'ü'],
            ['a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'v', 'v', 'v', 'v', 'v'],
            $char_with_tone
        );

        return $char_without_tone;
    }

    /**
     * 转成汉语拼音首字母
     * param $input_char String  需要转换的汉字
     * param $delimiter  String   转换之后拼音之间分隔符
     */
    public function transformUcwords($input_char, $delimiter = '')
    {
        $char_without_tone = ucwords($this->transformWithoutTone($input_char, ' ', true));
        $ucwords = preg_replace('/[^A-Z]/', '', $char_without_tone);
        if (!empty($delimiter)) {
            $ucwords = implode($delimiter, str_split($ucwords));
        }

        return $ucwords;
    }

    /**
     * 获取英文姓名首字母
     *
     * @param  [string] $name 英文名
     * @return string
     */
    public function getFirstCharacter($name)
    {
        $words = explode(' ', $name);
        $letters = '';
        foreach ($words as $word) {
            $letters .= $word{0};
        }

        return strtoupper($letters);
    }

    /**
     * 获取中文名
     *
     * @param  [string] $name 中文名
     * @return string
     */
    public function getFirstName($name)
    {
        if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", "$name")) {
            return mb_substr($name, -2, 2, 'utf-8');
        }
        //return substr($name, 0, 2);
        return strtoupper($this->getFirstCharacter($name));
    }
}
