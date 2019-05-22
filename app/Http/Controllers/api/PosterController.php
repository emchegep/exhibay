<?php

namespace App\Http\Controllers\api;

use App\Poster;
use App\Comment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Spatie\PdfToImage\Pdf;
use Illuminate\Notifications\Notifiable;


class PosterController extends Controller
{
    use Notifiable;
//Display all posters together with  users who uploaded them
    public function index()
    {
        //$posters= auth()->user()->posters;
        $posters=Poster::all();
        // auth()->user()->notify(new PosterUploadedNotification);
        // $posters = Poster::with('user:id,first_name,last_name,email,created_at,updated_at')->get();
        return response()->json(['message'=>'Fetched posters successfully','data'=>$posters],200);
        // return view('welcome',['posters'=>$posters]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public  function userPosters() {

        if($posters= auth()->user()->posters){
            return response()->json(['success'=>true,'message'=>'posters retrieved successfully','data'=>$posters],200);
        }
        else {
            return response()->json(['success'=>false,'message'=>'could not retrieve posters'],201);
        }
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*
     * store a newly created poster
     */
    public function store(Request $request)
    {
        //

        $poster_pdf_url='posters/pdfs';
        $poster_image_url='posters/images';
        // $pdf_image_url='posters/pdfs/images';
        // $poster_video_url= 'posters/videos';

        $poster_pdf_url=Storage::disk('public')->put($poster_pdf_url,$request->poster_pdf);
        $poster_image_url=Storage::disk('public')->put($poster_image_url,$request->poster_image);
        $poster = new Poster($request->all());
        $poster->poster_pdf_url=$poster_pdf_url;
        $poster->poster_image_url=$poster_image_url;
        $poster->poster_video_url=$request->video;
        $poster->user_id = Auth::id();

       $pdfImage= new Pdf($request->poster_pdf);

        $linkArray = explode("/", $poster_pdf_url);
        $lastValue = end($linkArray);

        $linkArray = explode('.',$lastValue);
        $image_file_name=current($linkArray).'.png';

        $pathToImage = "posters/pdfs/images/" . $image_file_name;
       $result = $pdfImage->saveImage(Storage::disk('public')->put($pathToImage,$image_file_name));
        $poster->pdf_image_url = $pathToImage;


        if (auth()->user()->posters()->save($poster))
        {
          //  $poster->notify(new PosterUploadedNotification);
            return response()->json(['message'=>'Poster uploaded successfully','data'=>$poster],200);
        }
        else
        {
            return response()->json(['message'=>'Poster cannot be added'],500);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Poster  $poster
     * @return \Illuminate\Http\Response
     */
    /*
     * Display the specified poster together with the user who upload it
     */
    public function show(Poster $poster)
    {
        //
        // $poster =Poster::with('user:id,first_name,last_name,email')->where('id',$poster->id)->get();
        $poster = auth()->user()->posters()->find($poster->id);
        if (!$poster){
            return response()->json(['message'=>'Poster with id '.$poster->id.' not found'], 400);
        } else {
            return response()->json(['message'=>'poster found','data'=>$poster], 200);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Poster  $poster
     * @return \Illuminate\Http\Response
     */
    public function edit(Poster $poster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poster  $poster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poster $poster)
    {
        $poster = auth()->user()->posters()->find($poster->id);
        if (!$poster){
            return response()->json(['message'=>'Poster with id '.$poster->id.' not found'], 400);
        }
        $updated = $poster->fill($request->all())->save();
        if ($updated){
            return response()->json(['message'=>'poster updated successfully','data'=>$poster],200);
        } else {
            return response()->json(['message'=>'poster could not be updated']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Poster  $poster
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poster $poster)
    {
        //
        $poster = auth()->user()->posters()->find($poster->id);
        if (!$poster) {
            return response()->json(['message'=>'Poster with id '.$poster->id.' not found'], 400);
        }
        if ($poster->delete()) {
            return response()->json(['message'=>'Poster deleted successfully','data'=>$poster], 200);
        }
        else {
            return response()->json(['message'=>'Poster could not be deleted'],401);
        }

    }

    /*
     * store comment in the db
     */
    public function storeComment(Request $request,$posterId) {
        $comment = new Comment();
        $comment->body = $request->body;
        $comment->user_id = $request->user()->id;
        $comment->poster_id = $posterId;

        if($comment->save()) {
            return response()->json(['message'=>'comment sent successfully'],200);
        }
    }
    //display all poster comments
    public function getComments($posterId) {

        $poster = auth()->user()->posters()->find($posterId);
        $comments=$poster->comments;
        return response()->json(['message'=>'comments retrieved successfully','data'=>$comments]);
    }
    //update comment
    public function updateComment(Request $request,$posterId,$commentId){
        $poster = auth()->user()->posters()->find($posterId);
        $comment=$poster->comments()->where('id',$commentId)->firstOrFail();
        // $data = ['user_id'=>auth()->id(),'poster_id'=>$posterId,'body'=>$request->body];
        $comment->body=$request->body;
        $updated=$comment->save();
        //  $users = User::all();
        //Notification::send($users, new posterUploadedNotification);
        $user = auth()->user();
        $comment->nofify(new PosterUploadedNotification);
        if($updated) {
            return response()->json(['success'=>true,'message'=>'comment updated successfully','data'=>$comment],200);
        } else {
            return response()->json(['success'=>false,'message'=>'comment could not be deleted'],500);
        }
    }
    //delete comment
    public function deleteComment($posterId,$commentId) {
        $poster = auth()->user()->posters()->find($posterId);
        $comment=$poster->comments()->where('id',$commentId)->first();
        if ($comment->delete()){
            return response()->json(['message'=>'Comment deleted successfully','data'=>$comment],200);
        } else {
            return response()->json(['message'=>'comment could not be deleted'],500);
        }

    }
}
