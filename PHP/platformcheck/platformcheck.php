<?php

    class Platformcheck {

        public $platform = 'unknown';

        public $device = 'unknown';

        function __construct() {

            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $platform = 'unknown';

            $device = 'unknowm';

            if(preg_match('/iPhone/i', $userAgent)) {

                $platform = 'ios';

                $device = 'smartphone';

            } elseif(preg_match('/iPad/i', $userAgent)) {

                $platform = 'ios';

                $device = 'tablet';

            } elseif(preg_match('/Android/i', $userAgent)) {

                if(preg_match('/Mobile/i', $userAgent)) {

                    $platform = 'android';

                    $device = 'smartphone';

                } else {

                    $platform = 'android';

                    $device = 'tablet';

                }

            } elseif(preg_match('/linux/i', $userAgent)) {

                $platform = 'linux';

                $device = 'desktop';

            } elseif(preg_match('/macintosh|mac os x/i', $userAgent)) {

                $platform = 'osx';

                $device = 'desktop';

            } elseif(preg_match('/windows|win32/i', $userAgent)) {

                $platform = 'windows';

                $device = 'desktop';

            }

            $this->platform = $platform;

            $this->device = $device;

        }

        function platform() {

            return $this->platform;

        }

        function device() {

            return $this->device;

        }

        function test() {

            echo "<h3><b>Platformcheck</h3><br /><b>Platform:</b> ".$this->platform."<br /><b>Device: ".$this->device."</b></p>";

        }

    }
