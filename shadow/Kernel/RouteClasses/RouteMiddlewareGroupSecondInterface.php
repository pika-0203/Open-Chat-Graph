<?php

namespace Shadow\Kernel\RouteClasses;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface RouteMiddlewareGroupSecondInterface extends RouteSecondInterface
{
    /**
     * Adds a path.
     * 
     * @param string $path The first argument must be a string representing the path.  
     * 
     * * **Optional:** If you want to define the controller(s) explicitly instead of determining them dynamically from the path,
     *     pass them as additional arguments after the path string.  
     *     `['explicitControllerClassName ', 'methodName'] + ['optionalRequestMethod']`  
     * 
     * * **Example:** `path('mypage')` `path('mypage/post@POST@DELETE')` `path('mypage/posts/{post_id}')`  
     *     `path('mypage', [Mypage::class, 'index'])`  
     *     `path('posts/likes/{post_id}@POST@DELETE', [PostLikes::class, 'like', 'POST'], [PostLikes::class, 'disLike', 'DELETE'])`
     * 
     * @return RouteMiddlewareGroupSecondInterface The instance of the Route class.
     * 
     * @throws \InvalidArgumentException
     */
    public function path(string|array ...$path): RouteMiddlewareGroupSecondInterface;
}
