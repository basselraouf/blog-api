<?php

namespace App\Http\Controllers;

use App\Mail\CommentAdded;
use App\Models\comment;
use App\Models\post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
       /**
     * @OA\Post(
     *     path="/api/comments/add",
     *     summary="Add a new comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="post_id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Great post!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment added successfully"
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
                'content' => 'required|string',
                'post_id' => 'required|exists:posts,id',
            ]);

            $comment = comment::create([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'post_id' => $request->post_id,
            ]);

            $post = post::find($request->post_id);

            Mail::to($post->user->email)->send(new CommentAdded($post));

            return response()->json([
                'message' => 'Comment added successfully',
                'comment' => $comment,
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
     *     path="/api/comments/delete/{id}",
     *     summary="Delete a comment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $comment->delete();

            return response()->json([
                'message' => 'Comment deleted successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
