<?php

/**
 * The Short controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */
class ShortController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
    }

    /**
     * This method controls what happens when you move to /Short/index in your app.
     * Gets all Shorts (of the user).
     */
    public function index()
    {
        Auth::checkAuthentication();
        $this->View->render('short/index');
    }

    /**
     * This method controls what happens when you move to /dashboard/create in your app.
     * Creates a new Short. This is usually the target of form submit actions.
     * POST request.
     */
    public function create()
    {
        Auth::checkAuthentication();
        ShortModel::createShort(Request::post('url'));
        Redirect::to('short/index');
    }
    
    public function redirect()
    {
        //ShortModel::createShort(Request::get('code'));
        
        $urlRow = ShortModel::getUrlFromDb(Request::get('code'));
        if(!empty($urlRow)){
        Redirect::out($urlRow->long_url);
        } else {
        Redirect::to('error');
        }
        
        
        //Redirect::to('short/index');
    }

}
