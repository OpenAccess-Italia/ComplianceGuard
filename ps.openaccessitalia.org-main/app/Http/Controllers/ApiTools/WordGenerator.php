<?php

namespace App\Http\Controllers\ApiTools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WordGenerator extends Controller
{
    //
    public function generateAlpha($minlength,$maxlength){
        $word = "";
        while(strlen($word) <= $minlength || strlen($word) >= $maxlength){
            $word = $this->get();
        }
        return $word;
    }

    private function get(){
        $file = \Storage::path("words/words_alpha.txt");
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);
        $rand_text = $file_arr[$rand_index];
        return trim(preg_replace('/\s\s+/', '', $rand_text));
    }

    public function generateNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    public static function makeToken($lenght){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited
        for ($i=0; $i < $lenght; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max-1)];
        }
        return $token;
    }

    private static function crypto_rand_secure($min,$max){
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
}
