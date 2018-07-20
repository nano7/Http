<?php namespace Nano7\Http;

interface Responsable
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse();
}