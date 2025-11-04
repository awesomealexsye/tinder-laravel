<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Tinder-like Dating App API",
 *     version="1.0.0",
 *     description="RESTful API for a Tinder-like dating application with user profiles, recommendations, and matching functionality",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 *
* @OA\Server(
 *     url="https://paylaptodo.shop",
 *     description="Production Server",
 *     @OA\ServerVariable(
 *         serverVariable="host",
 *         default="http://localhost:8000"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Enter your bearer token in the format: Bearer {token}"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints"
 * )
 *
 * @OA\Tag(
 *     name="People",
 *     description="User discovery and interaction endpoints"
 * )
 */
abstract class Controller
{
    //
}
