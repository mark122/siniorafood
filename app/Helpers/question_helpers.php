<?php
/**
 * File name: question_helpers.php
 * Last modified: 14/06/21, 5:27 PM
 * Author: NearCraft - https://codecanyon.net/user/nearcraft
 * Copyright (c) 2021
 */

if(!function_exists('formatQuestionProperty')) {
    /**
     * Format Question for the Exam
     * @param $question
     * @param $qType
     * @return null|string|string[]
     */
    function formatQuestionProperty($question, $qType) {
        if($qType == "FIB") {
            return replaceBlanksWithPlaceholder($question);
        }
        return $question;
    }
}

if(!function_exists('formatOptionsProperty')) {
    /**
     * Format Options for the Exam
     * @param $options
     * @param $qType
     * @param $question
     * @return array|int
     */
    function formatOptionsProperty($options, $qType, $question) {
        $newOptions = [];
        switch($qType) {
            case "MTF":
                $leftOptions = [];
                $rightPairs = [];
                $matches = [];
                $pairs = [];
                foreach ($options as $key => $option) {
                    array_push($leftOptions, $option['option']);
                    array_push($rightPairs, $option['pair']);
                }
                foreach ($leftOptions as $key => $option) {
                    array_push($matches, ['id' => md5(config('qwiktest.matching_option_salt').$key), 'value' => $option]);
                }
                foreach ($rightPairs as $key => $pair) {
                    array_push($pairs, ['id' => md5(config('qwiktest.matching_pair_salt').$key), 'value' => $pair, 'code' => '']);
                }
                shuffle($pairs);
                foreach ($pairs as $key => &$pair) {
                    $pair['code'] = covertToCharacter($key);
                }
                $newOptions['matches'] = $matches;
                $newOptions['pairs'] = $pairs;
                return $newOptions;
                break;
            case "SAQ":
            case "LAQ":
                return [];
                break;
            case "FIB":
                return count(getBlankItems($question));
                break;
            case "ORD":
                foreach ($options as $key => $option) {
                    array_push($newOptions, ['id' => md5(config('qwiktest.ordering_option_salt').$key), 'value' => $option['option'], 'code' => '']);
                }
                shuffle($newOptions);
                foreach ($newOptions as $key => &$option) {
                    $option['code'] = covertToCharacter($key);
                }
                return $newOptions;
                break;
            default:
                foreach ($options as $option) {
                    array_push($newOptions, $option['option']);
                }
                return $newOptions;
                break;
        }
    }
}

if(!function_exists('formatAnswerProperty')) {
    /**
     * Format Answer Property for the Exam
     * @param $qType
     * @return array|string
     */
    function formatAnswerProperty($qType) {
        switch($qType) {
            case "MMA":
            case "MTF":
            case "FIB":
            case "ORD":
                return [];
                break;
            case "SAQ":
            case "LAQ":
            default:
                return '';
                break;
        }
    }
}

if(!function_exists('validateMSA')) {
    /**
     * Validate Multiple Choice Single Answer & True or False Questions
     * @param $correctAnswer
     * @param $userResponse
     * @return bool
     */
    function validateMSA($correctAnswer, $userResponse) {
        if((int) $userResponse == (int) $correctAnswer) {
            return true;
        }
        return false;
    }
}


if(!function_exists('validateMMA')) {
    /**
     * Validate Multiple Choice Multiple Answer Question
     * @param $correctAnswer
     * @param $userResponse
     * @return bool
     */
    function validateMMA($correctAnswer, $userResponse) {
        $x = array_values($userResponse);
        $y = array_values($correctAnswer);

        sort($x);
        sort($y);

        if($x == $y) {
            return true;
        }
        return false;
    }
}

if(!function_exists('validateFIB')) {
    /**
     * Validate Fill in the Blank Question
     * @param $correctAnswer
     * @param $userResponse
     * @return bool
     */
    function validateFIB($correctAnswer, $userResponse) {
        $x = array_values($userResponse);
        $y = array_values($correctAnswer);

        $x = array_map( 'strtolower', $x );
        $y = array_map( 'strtolower', $y );

        if($x == $y) {
            return true;
        }
        return false;
    }
}

if(!function_exists('validateMTF')) {
    /**
     * Validate Match The Following Question
     * @param $options
     * @param $userResponse
     * @param bool $answerFlag
     * @return array|bool
     */
    function validateMTF($options, $userResponse, $answerFlag = false) {
        $correctMatch = [];
        $responseMatch = [];
        foreach ($options as $key => $option) {
            array_push($correctMatch, md5(config('qwiktest.matching_pair_salt').$key));
        }
        foreach ($userResponse as $item) {
            if(is_array($item)) {
                array_push($responseMatch, $item['id']);
            } else {
                $object = json_encode($item, true);
                array_push($responseMatch, $object['id']);
            }
        }
        $x = array_values($correctMatch);
        $y = array_values($responseMatch);

        if($answerFlag) {
            return $correctMatch;
        }

        if($x == $y) {
            return true;
        }
        return false;
    }
}

if(!function_exists('validateSAQ')) {
    /**
     * @param $options
     * @param $userResponse
     * @return bool
     */
    function validateSAQ($options, $userResponse) {
        $possibleAnswers = [];
        foreach ($options as $option) {
            array_push($possibleAnswers, $option['option']);
        }

        if (in_array($userResponse, $possibleAnswers))
        {
            return true;
        }
        return false;
    }
}

if(!function_exists('validateORD')) {
    /**
     * Validate Sequence Question
     * @param $options
     * @param $userResponse
     * @return bool
     */
    function validateORD($options, $userResponse, $answerFlag = false) {
        $correctOrder = [];
        $responseOrder = [];
        foreach ($options as $key => $option) {
            array_push($correctOrder, md5(config('qwiktest.ordering_option_salt').$key));
        }
        foreach ($userResponse as $item) {
            if(is_array($item)) {
                array_push($responseOrder, $item['id']);
            } else {
                $object = json_decode($item, true);
                array_push($responseOrder, $object['id']);
            }
        }

        $x = array_values($correctOrder);
        $y = array_values($responseOrder);

        if($answerFlag) {
            return $correctOrder;
        }

        if($x == $y) {
            return true;
        }
        return false;
    }
}

if(!function_exists('formatImportOptionsProperty')) {
    /**
     * Format Options when Importing Questions
     * @param $options
     * @param $qType
     * @return array|int
     */
    function formatImportOptionsProperty($options, $qType) {
        $newOptions = [];
        if($qType == "FIB" || $qType == "LAQ") {
            return $newOptions;
        }
        if($qType == "TOF") {
            return [
                ['option' => strval($options[0]), 'partial_weightage' => 0],
                ['option' => strval($options[1]), 'partial_weightage' => 0]
            ];
        }
        if($qType == "MTF") {
            foreach ($options as $option) {
                if($option != null || $option != "") {
                    $match = explode (",", $option);
                    array_push($newOptions, ['option' => strval($match[0]), 'pair' => strval($match[1]), 'partial_weightage' => 0]);
                }
            }
            return $newOptions;
        }
        foreach ($options as $option) {
            if($option != null || $option != "") {
                array_push($newOptions, ['option' => strval($option), 'partial_weightage' => 0]);
            }
        }
        return $newOptions;
    }
}

if(!function_exists('formatImportAnswerProperty')) {
    /**
     * Format Answer Property when Importing Questions
     * @param $correctAnswer
     * @param $qType
     * @param $question
     * @return array|string
     */
    function formatImportAnswerProperty($correctAnswer, $qType, $question) {
        switch($qType) {
            case "MMA":
                $correctAnswers = [];
                $answers = explode (",", $correctAnswer);
                foreach ($answers as $answer) {
                    array_push($correctAnswers, $answer);
                }
                return $correctAnswers;
                break;
            case "MTF":
            case "ORD":
                return [];
                break;
            case "SAQ":
            case "LAQ":
                return '';
                break;
            case "FIB":
                return getBlankItems($question);
                break;
            default:
                return $correctAnswer;
                break;
        }
    }
}

if(!function_exists('formatImportPreferencesProperty')) {
    /**
     * Format Preferences Property when Importing Questions
     * @param $qType
     * @return array|string
     */
    function formatImportPreferencesProperty($qType) {
        switch($qType) {
            case "LAQ":
                return ['word_limit' => false, 'max_words' => 0, 'min_words' => 0];
                break;
            case "FIB":
                return ['case_sensitive' => false];
                break;
            default:
                return null;
                break;
        }
    }
}

if(!function_exists('replaceBlanksWithPlaceholder')) {
    /**
     * Function to replace Fill in the Blank question blanks with placeholder
     * @param $text
     * @return null|string|string[]
     */
    function replaceBlanksWithPlaceholder($text) {
        return preg_replace_callback(
            "/(##)(.*?)(##)/",
            function($m) {
                static $id = 0;
                $id++;
                return "[".$id."] "."______";
            },
            $text);
    }
}

if(!function_exists('replaceBlanksWithContent')) {
    /**
     * Function to replace Fill in the Blank question blanks with content
     * @param $text
     * @return null|string|string[]
     */
    function replaceBlanksWithContent($text) {
        return preg_replace_callback( '/(\_\_\_\_\_\_)/', function($match) use( &$blank_contents) {
            return '##'.array_shift( $blank_contents).'##';
        }, $text);
    }
}

if(!function_exists('replaceBlanksWithInput')) {
    /**
     * Function to replace Fill in the Blank question blanks with text input
     * @param $text
     * @return null|string|string[]
     */
    function replaceBlanksWithInput($text) {
        return preg_replace("/(##)(.*?)(##)/", "<input type='text' />", $text);
    }
}

if(!function_exists('getBlankItems')) {
    /**
     * function to get blank values from the Fill in the Blank Question
     * @param $str
     * @return array
     */
    function getBlankItems($str) {
        $startDelimiter = "##";
        $endDelimiter = "##";
        $contents = array();
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($str, $endDelimiter, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }
        return $contents;
    }
}


if(!function_exists('covertToCharacter')) {
    /**
     * Converts Numeric into Character for Question Options
     * @param $value
     * @return string
     */
    function covertToCharacter($value) {
        $characters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        return $characters[$value];
    }
}

if(!function_exists('getQuestionScoringDISC')) {
    function getQuestionScoringDISC($questions,$indexNumber){
        
            $discTable = array(
                  0 => 
                  array(
                    'D' => 'B',
                    'I' => 'D',
                    'S' => 'A',
                    'C' => 'C',
                  ),
                  1 => 
                  array(
                    'D' => 'A',
                    'I' => 'C',
                    'S' => 'D',
                    'C' => 'B',
                  ),
                  2 => 
                  array(
                    'D' => 'C',
                    'I' => 'B',
                    'S' => 'A',
                    'C' => 'D',
                  ),
                  3 => 
                  array(
                    'D' => 'A',
                    'I' => 'D',
                    'S' => 'C',
                    'C' => 'B',
                  ),
                  4 => 
                  array(
                    'D' => 'D',
                    'I' => 'B',
                    'S' => 'C',
                    'C' => 'A',
                  ),
                  5 => 
                  array(
                    'D' => 'B',
                    'I' => 'A',
                    'S' => 'D',
                    'C' => 'C',
                  ),
                  6 => 
                  array(
                    'D' => 'C',
                    'I' => 'D',
                    'S' => 'B',
                    'C' => 'A',
                  ),
                  7 => 
                  array(
                    'D' => 'B',
                    'I' => 'A',
                    'S' => 'D',
                    'C' => 'C',
                  ),
                  8 => 
                  array(
                    'D' => 'D',
                    'I' => 'A',
                    'S' => 'C',
                    'C' => 'B',
                  ),
                  9 => 
                  array(
                    'D' => 'C',
                    'I' => 'B',
                    'S' => 'D',
                    'C' => 'A',
                  ),
                  10 => 
                  array(
                    'D' => 'A',
                    'I' => 'D',
                    'S' => 'C',
                    'C' => 'B',
                  ),
                  11 => 
                  array(
                    'D' => 'D',
                    'I' => 'C',
                    'S' => 'A',
                    'C' => 'B',
                  ),
                  12 => 
                  array(
                    'D' => 'B',
                    'I' => 'A',
                    'S' => 'D',
                    'C' => 'C',
                  ),
                  13 => 
                  array(
                    'D' => 'C',
                    'I' => 'D',
                    'S' => 'B',
                    'C' => 'A',
                  ),
                  14 => 
                  array(
                    'D' => 'D',
                    'I' => 'A',
                    'S' => 'C',
                    'C' => 'B',
                  ),
                  15 => 
                  array(
                    'D' => 'A',
                    'I' => 'B',
                    'S' => 'C',
                    'C' => 'D',
                  ),
                  16 => 
                  array(
                    'D' => 'B',
                    'I' => 'C',
                    'S' => 'D',
                    'C' => 'A',
                  ),
                  17 => 
                  array(
                    'D' => 'C',
                    'I' => 'A',
                    'S' => 'B',
                    'C' => 'D',
                  ),
                  18 => 
                  array(
                    'D' => 'D',
                    'I' => 'B',
                    'S' => 'C',
                    'C' => 'A',
                  ),
                  19 => 
                  array(
                    'D' => 'A',
                    'I' => 'D',
                    'S' => 'C',
                    'C' => 'B',
                  ),
                  20 => 
                  array(
                    'D' => 'A',
                    'I' => 'B',
                    'S' => 'C',
                    'C' => 'D',
                  ),
                  21 => 
                  array(
                    'D' => 'D',
                    'I' => 'C',
                    'S' => 'B',
                    'C' => 'A',
                  ),
                  22 => 
                  array(
                    'D' => 'D',
                    'I' => 'B',
                    'S' => 'A',
                    'C' => 'C',
                  ),
                  23 => array(
                    'D' => 'D',
                    'I' => 'C',
                    'S' => 'A',
                    'C' => 'B',
                  ),
            );
            
            $discAlphabet = ["A","B","C","D"];
            $score = 0;
            foreach ($questions as $key => $value) { 
                if ($value['user_answer']) {
                    if($discTable[$key][$indexNumber] === $discAlphabet[$value['user_answer'] - 1]) {
                        $score++;
                    }
                }
            }
        
        return $score;
    }
}