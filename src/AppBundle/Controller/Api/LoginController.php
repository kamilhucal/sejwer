<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Swagger\Annotations as SWG;

/**
 * @RouteResource("login", pluralize=false)
 *
 */
class LoginController extends FOSRestController implements ClassResourceInterface
{
    /**
     *  @SWG\Parameter(
     *     name="Login Credentials",
     *     in="body",
     *     description="Username and not hashed password passed in body",
     *     required=true,
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="string",
     *          property="username"
     *          ),
     *          @SWG\Property(
     *          type="string",
     *          property="password"
     *          )
     *      )
     *  )
     *  @SWG\Response (
     *      response=200,
     *      description="Success response - Receiving JWT Token, for future validation",
     *      examples={"application/json" : {"token": "eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTg5ODI4OCwiZXhwIjoxNTA2Mzk4Mjg4fQ.IJlxMu-dDXEy9wW_JDEWzgFTDfpFd3Oe2IUl-jXXT_vDiB6MQx4d0mOVbC3K16_nWyVc3qjtLoWLn48QHdHiedIvwf_MY_W3zFs3JQXJ5aJdcAGDu1Zks8n3D6x22M1kxwJ0erXlS8dRUwQmeeYElhMJrVymyKUsMA1yxDFOrC7f-8wCvIDVgPNckkji6DiC3i5eVcK7GQ1PYxgx-be2TU7KrPaMqbI1DOeBaxUOAx5xRbslQPMTDuerrrTjFh9fHmUzwYw_wNDBYeb3ZxwBwov4fG00frhb1axUJvZY28fpvX5Z1dd26z_0ScxTKP4NbObaZ-eHvRBzPAb4xiE-fVXXO-V4iLqfnWPXQjB7tXTK_mVp40OjQdzWW1Oj0hZbfyBvn7duP3pn-jBy9hISxd_jAjkWXASZL9NuN1PxvQtvW92vTmcFBEIBTvIl6zIIqb1XBY3_qoAa0XCr6IhOf1KHqFmLLsRBhqulYfY5O8-8lrIsafMfECQ5hmD0ec3U6SLQadPeH4OCAboFPSqKWPzN4kz7ii-9KPrxQW8kwCH-8yxdb8pxmMmmKaUXZiJQfB1edlbWflj7ZzoObARq9Yeubk0b6Qm7fsaTLIzBId4RQdjdXuscG_FbzkkZWAa54YwROcKQGAcZB8quNJK-wWvEeLoUup0SQ6BgrU57fdo"}},
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="string",
     *          property="token"
     *          ),
     *      ),
     *  ),
     *  @SWG\Response(
     *     response=401,
     *     description="Error message regarding incorrect login data ",
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="integer",
     *          property="code"
     *          ),
     *      @SWG\Property(
     *          type="string",
     *          property="message"
     *          ),
     *      ),
     *     examples={"application/json" : { "code":401,"message":"Bad credentials" } }
     *  )
     *  @SWG\Tag(name="user")
     *
     *
     *
     */
    public function postAction()
    {
        // route handled by Lexik JWT Authentication Bundle
        throw new \DomainException('You should never see this');
    }
}