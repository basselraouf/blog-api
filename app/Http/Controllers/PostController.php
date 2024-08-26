<?php

namespace App\Http\Controllers;

use App\Models\post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts/all",
     *     summary="Get all posts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of posts"
     *     )
     * )
     */
    public function index()
    {
        $posts = post::with(['user'=> function ($query){
            $query->select('id','name');
        }])->paginate(5);
        return response()->json($posts);
    }

   /**
     * @OA\Post(
     *     path="/api/posts/create",
     *     summary="Create a new post",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Sample Title"),
     *                 @OA\Property(property="content", type="string", example="Sample Content")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post,
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/posts/update/{id}",
     *     summary="Update an existing post",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Title"),
     *                 @OA\Property(property="content", type="string", example="Updated Content")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try{

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $post = Post::findOrFail($id);

            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json([
                'message' => 'Post updated successfully',
                'post' => $post,
            ]);

        } catch (Exception $e) {

            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/posts/delete/{id}",
     *     summary="Delete a post",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function destroy(post $post, $id)
    {
        try {
            $post = Post::findOrFail($id);

            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'You do not have permission to delete this post',
                ]);
            }

            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully',
            ]);
        }catch(Exception $e){
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
