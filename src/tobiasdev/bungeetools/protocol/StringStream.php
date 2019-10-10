<?php

namespace tobiasdev\bungeetools\protocol;

use Exception;
use pocketmine\utils\Binary;

use pocketmine\utils\MainLogger;

/**
 * Class StringStream
 * @package tobiasdev\bungeetools\protocol
 * @author TobiasDev
 * Class for decoding data from packet reply
 * Implements Java's readUTF() as readString()
 */
class StringStream
{
    private $buffer;
    private $pointer = 0;

    public function __construct(String $toread)
    {
        $this->buffer = $toread;
    }

    public function readString(): ?String
    {
        try {
            // read first two length bytes
            $length = Binary::readShort($this->buffer{$this->pointer} . $this->buffer{$this->pointer + 1});
            // add 2 to pointer
            $this->changePointer($this->pointer + 2);
            // read length bytes far and append to string
            $d = "";
            for ($i = $this->pointer; $i < $this->pointer + $length; $i++) {
                $d .= $this->buffer{$i};
            }
            $this->pointer += $length;
            return $d;
        } catch (Exception $e) {
            MainLogger::getLogger()->warning("Error while decoding string: " . $this->buffer . " ( " . $e->getMessage() . ", LINE: " . $e->getLine() . " )");
            MainLogger::getLogger()->warning($e->getTraceAsString());
            return null;
        }
    }

    public function readInt(): ?int
    {
        try {
            $offset = $this->pointer;
            return Binary::readInt($this->buffer{$offset} . $this->buffer{$offset + 1} . $this->buffer{$offset + 2} . $this->buffer{$offset + 3});
        } catch (Exception $e) {
            MainLogger::getLogger()->warning("Error while decoding int: " . $this->buffer . " ( " . $e->getMessage() . ", LINE: " . $e->getLine() . " )");
            return null;
        }
    }

    public function changePointer(int $newpointer)
    {
        MainLogger::getLogger()->debug("Changed pointer from " . $this->pointer . " to " . $newpointer);
        $this->pointer = $newpointer;
    }
    public function readUnsignedShort(): ?int {
        try{
            return Binary::readUnsignedVarInt($this->buffer, $this->pointer);
        }catch (Exception $e) {
            var_dump("Target Buffer: " . $this->buffer);
            MainLogger::getLogger()->warning("Error while decoding unsignedShort: " . $this->buffer . " ( " . $e->getMessage() . ", LINE: " . $e->getLine() . " )");
            return null;
        }
    }
}