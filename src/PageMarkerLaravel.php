<?php

namespace Adods\PageMarkerLaravel;

use Adods\PageMarker\PageMarker;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Redirector;

class PageMarkerLaravel extends PageMarker
{
    /**
     * Laravel Request
     *
     * @var Illuminate\Http\Request
     */
    private $request;

    /**
     * Laravel Redirector
     *
     * @var Illuminate\Routing\Redirector
     */
    private $redirector;

    /**
     * Laravel Session
     *
     * @var Illuminate\Contracts\Session\Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param Illuminate\Http\Request $request 
     * @param Illuminate\Routing\Redirector $redirector
     * @param Illuminate\Contracts\Session\Session $session
     */
    public function __construct(
        Request $request,
        Redirector $redirector,
        Session $session
    ) {
        $this->request = $request;
        $this->redirector = $redirector;
        $this->session = $session;
    }

    /**
     * Laravel Override
     *
     * @return void
     */
    public function setNameFromUrl()
    {
        $this->setName($this->request->path() ?: '_');
    }

    /**
     * Laravel Override
     *
     * @return string
     */
    protected function getCurrentUrl()
    {
        return $this->request->fullUrl();
    }

    /**
     * Laravel Override. Return Illuminate\Http\RedirectResponse when conditions
     * are met
     *
     * @param boolean $bypassRedirection Skip redirection
     * @return null|Illuminate\Http\RedirectResponse
     */
    public function init($bypassRedirection = false)
    {
        if (is_null($this->base)) {
            $this->setBase($this->request->query());
        }
        
        parent::init(true);

        if (!$bypassRedirection) {
            return $this->prepareRedirection();
        }

        return null;
    }

    /**
     * Laravel Override. Return Illuminate\Http\RedirectResponse when conditions
     * are met
     *
     * @return null|Illuminate\Http\RedirectResponse
     */
    protected function prepareRedirection()
    {
        // If forget key detected, then reset
        if (isset($this->base[$this->forgetKey])) {
            $this->forget();
            return $this->redirect($this->url);
        }

        // If there's data in the base then nothing happened
        if (count($this->base)) {
            return;
        }

        $session = $this->retrieveSession();

        // If there's no data from session, also nothing happened
        if (empty($session)) {
            return;
        }

        $query = http_build_query($session);
        $url = $this->url.'?'.$query;

        return $this->redirect($url);
    }

    /**
     * Laravel Override
     *
     * @param string $url URL String
     * @return Illuminate\Http\RedirectResponse Laravel Redirect Response
     */
    protected function redirect($url)
    {
        return $this->redirector->to($url, 307);
    }

    /**
     * Laravel Override
     *
     * @return void
     */
    public function forget()
    {
        $this->session->forget($this->sessionName());
    }

    /**
     * Laravel Override
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->session->put($this->sessionName(), $this->base);
    }

    /**
     * Laravel Override
     *
     * @return void
     */
    protected function retrieveSession()
    {
        return $this->session->get($this->sessionName());
    }
}