<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Models
use App\Models\Posts;
use App\Models\Settings;
use App\Models\Category;
use App\Models\Event;
use App\Models\Writter;
use App\Models\Advertise;
use App\Models\User;
use App\Models\Video;

use DataTables;

class AdminController extends Controller
{   

    public function users(Request $request){
        $page = "Registered Users";

        if ($request->ajax()) {
            $data = User::latest()->get();
            return Datatables::of($data)
                     ->addIndexColumn()
                     ->addColumn('action', function ($data) {
                         $btns = '<div class="btn-group"><a href="users/user/'.$data->id.'" class="edit btn btn-primary btn-sm">View/Edit</a><a href="users/destroy/'.$data->id.'" class="btn btn-danger btn-sm">Delete</a></div>';
                            
                         return $btns;
                     })
                    
                     ->rawColumns(['action'])
                     ->make(true);
        }
        return view('admin.users.index', compact('page'));
    }

    public function updateUserForm($id){
        $user = User::find($id);
        return view('admin.users.profile', compact('user'));
    }


    public function updateUserImage(Request $request, $id)
    {
        if (!$request->image) {
            toastr()->error('Image field if required!');
            return back();
        }
        $user = User::find($id);
        if ($request->image) {
            $dir ="storage/profile/";
            $new_image = $this->uploadImage($request->image, $dir);
            $user->image = $new_image;
            $user->save();
        }

        toastr()->success('Image Updated successfully');
        return back();
    }


    public function updateUser(Request $request, $id){
        $user = User::find($id);
        $input = $request->all();
        $user->fill($input)->save();

        if ($request->password) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
        }
        toastr()->success('User details updated successfully');
        return back();
    }




    public function uploadImage($image, $dir){
        $image_name = $image->getClientOriginalName();
        $new_name = time().$image_name;
        $image->move($dir, $new_name);
        return $new_name;
    }

    public function home(){
        $categories = Category::all();
        $events = Event::all();
        $posts = Posts::all();
        $latest_posts =Posts::latest()->take(10)->get();
        $latest_users =User::latest()->take(10)->get();
        $writers =User::where('is_writer', 1)->get();
        $admins =User::where('is_admin', 1)->get();
        $writer_requests = Writter::all();
        $advert_requests = Advertise::all();
        $users = User::all();
        $videos = Video::all();
        return view('admin.home', compact(
            'categories',
            'events',
            'posts',
            'writer_requests',
            'advert_requests',
            'users',
            'videos',
            'latest_posts',
            'latest_users',
            'writers',
            'admins'
        ));
    }

    public function settingsUpdateForm(){
        $page = "Update Settings";
        $settings = Settings::latest()->first();
        return view('admin.settings.update-settings', \compact('settings','page'));
    }

    public function settingsUpdate(Request $request){
        $logo = null;
        $settings = Settings::latest()->first();
        if($settings != null && $settings->site_logo){
            $logo = $settings->site_logo;
        }

        if($request->site_logo){
            $dir = "storage/settings/logo/";
            $logo = $this->uploadImage($request->site_logo, $dir);
        }

        if($settings != null){
            $input = $request->all();
            $settings->fill($input)->save();

            if($request->site_logo){
                $settings->site_logo = $logo;
                $settings->save();
            }
            return back();
        }else{
            Settings::create($request->all());
            if($request->site_logo){
                $settings->site_logo = $logo;
                $settings->save();
            }
            return back();
        }
    }

    public function categories(Request $request){
        $page = "Categories";

        if ($request->ajax()) {
            $data = Category::latest()->get();
            return Datatables::of($data)
                     ->addIndexColumn()
                     ->addColumn('action', function ($data) {
                         $btns = '<div class="btn-group"><a href="categories/'.$data->id.'" class="edit btn btn-primary btn-sm">View/Edit</a><a href="categories/destroy/'.$data->id.'" class="btn btn-danger btn-sm">Delete</a></div>';
                            
                         return $btns;
                     })
                    
                     ->rawColumns(['action'])
                     ->make(true);
        }
        return view('admin.category.index', compact('page'));

    }


    public function categoryCreateForm(){
        $page = "Create Category";
        return view("admin.category.create-category", compact('page'));
    }

    public function categoryCreate(Request $request){
        $image = null;
        if($request->image){
            $dir = "storage/categories/";
            $image = $this->uploadImage($request->image, $dir);
        }
        $category = new Category;
        $category->title = $request->title;
        $category->desc = $request->desc;
        $category->image = $request->image;
        $category->user_id = $request->user_id;
        $category->save();
        toastr()->success('Category created successfully');
        return back();
    }

    public function categoryUpdateForm(Request $request, $id){
        $category = Category::find($id);
        $page = "Create Category";
        return view("admin.category.update-category", compact('category','page'));
    }

    public function categoryUpdate(Request $request, $id){
        $category = Category::find($id);
        $image = $category->image;
        $category->fill($request->all())->save();

        if($request->image){
            $dir = "storage/categories/";
            $image =  $this->uploadImage($request->image, $dir);
            $category->image = $image;
            $category->save();
        }

        toastr()->success('Category updated successfully');
        return back();
    }


    public function categoryDestory($id){
        $category = Category::find($id);
        $category->delete();
        toastr()->success('Category deleted successfully');
        return back();
    }


    public function posts(Request $request)
    {
        $page = "posts";

        if ($request->ajax()) {
            $data = Posts::latest()->get();
            if (auth()->user()->is_writer) {
                $data = Posts::where('user_id', auth()->id())->latest()->get();
            }
            return Datatables::of($data)
                     ->addIndexColumn()
                     ->addColumn('action', function ($data) {
                         $btns = '<div class="btn-group"><a href="posts/'.$data->id.'" class="edit btn btn-primary btn-sm">View/Edit</a><a href="posts/destroy/'.$data->id.'" class="btn btn-danger btn-sm">Delete</a></div>';
                            
                         return $btns;
                     })
                    
                     ->rawColumns(['action'])
                     ->make(true);
        }
        
        return view('admin.posts.index', compact('page'));
    }

    public function postCreateForm(){
        $page = "Create Post";
        $categories = Category::latest()->get();
        return view('admin.posts.create-posts', compact('page', 'categories'));
    }

    public function postCreate(Request $request){
        $image = null;
        if($request->image){
            $dir = "storage/posts/";
            $image = $this->uploadImage($request->image, $dir);
        }

        $post = new Posts;
        $post->title=$request->title;
        $post->category_id=$request->category_id;
        $post->user_id=$request->user_id;
        $post->short_desc=$request->short_desc;
        $post->image=$image;
        $post->long_desc=$request->long_desc;
        $post->special=$request->special;
        $post->breaking=$request->breaking;
        $post->save();
        toastr()->success('Post created successfully');
        return back();

    }


    public function postUpdateForm($id){
        $post = Posts::find($id);
        $categories = Category::latest()->get();
        $page = "Create Posts";

        return view('admin.posts.update-post', compact('post', 'categories', 'page'));
    }

    public function postUpdate(Request $request, $id){
        $post = Post::find($id);
        $image = $post->image;
        if ($request->image) {
            $dir = "storage/posts/";
            $image =  $this->uploadImage($request->image, $dir);
            $post->image = $image;
            $post->save();
        }
        toastr()->success('Post updated successfully');
        return back();
    }

    public function postDestory($id){
        $post = Post::find($id);
        $post->delete();
        toastr()->success('Post deleted successfully');
        return back();
    }

    public function events(Request $request){
        $page = "events";
        $events = Event::latest()->get();
        return view('admin.events.index', compact('page', 'events'));
    }

    public function eventCreateForm(){
        $page = "Create Event";

        return view("admin.events.create-events", compact('page'));
    }

    public function eventCreate(Request $request){
        Event::create($request->all());
        toastr()->success('Event created successfully');
        return back();
    }

    public function eventUpdateForm($id){
        $event = Event::find($id);
        $page = "Create event";
        return view('admin.events.update-event', compact('event', 'page'));
    }

    public function eventUpdate(Request $request, $id){
        $event = Event::find($id);
        $event->fill($request->all())->save();
        toastr()->success('Event updated successfully');
        return back();
    }

    public function eventDestroy($id){
        $event = Event::find($id);
        $event->delete();
        toastr()->success('Event deleted successfully');
        return back();
    }

    public function writer_requests(){
        $page = "Writter Request";
        $writer_requests = Writter::latest()->get();
        return view('admin.writter.writter-requests', compact('writer_requests', 'page'));
    }

    public function advertiser_requests(){
        $page = "Advertise requests";
        $advert_requests = Advertise::latest()->get();
        return view('admin.advertise.advert-requests', compact('advert_requests', 'page'));
    }

    public function writer_destroy($id){
        $r = Writter::find($id);
        $r->delete();

        toastr()->success('Writer request deleted successfully!');
        return back();
    }

    public function advert_destroy($id){
        $r = Advertise::find($id);
        $r->delete();
        toastr()->success('Advert request deleted successfully!');
        return back();
    }

    public function approveWritter($id){
        $user = User::find($id);
        $user->is_writer = 1;
        $user->save();
        toastr()->success('Users role changed to writer successfully!');
        return back();
    }

    public function banWriter($id){
        $user = User::find($id);
        $user->is_writer = 0;
        $user->save();
        toastr()->success('User banned from accessing writers panel successfully!');
        return back();
    }


    public function videos(Request $request)
    {
        $page = "videos";
        $videos = Video::latest()->get();
        if (auth()->user()->is_writer) {
            $videos = Video::where('user_id', auth()->id())->latest()->get();
        }
        return view('admin.videos.index', compact('page', 'videos'));
    }
  
    public function videoCreateForm()
    {
        $page = "Create video";
        $categories = Category::latest()->get();
        
        return view('admin.videos.create-video', compact('page', 'categories'));
    }
    public function videoCreate(Request $request)
    {
        $image = null;
        if ($request->image) {
            $dir = "storage/videos/";
            $image =  $this->uploadImage($request->image, $dir);
        }
        $video = new Video;
        $video->url = $request->url;
        $video->image = $image;
        $video->user_id = $request->user_id;
        $video->category_id = $request->category_id;
        $video->title = $request->title;
        $video->save();
        toastr()->success('Video created successfully');
        return back();
    }
    public function videoUpdateForm($id)
    {
        $video = Video::find($id);
        $categories = Category::latest()->get();
        $page = "Create video";
        return view('admin.videos.update-video', compact('video', 'page', 'categories'));
    }
    public function videoUpdate(Request $request, $id)
    {
        $video = Video::find($id);
        $image = $video->image;
        
        $video->fill($request->all())->save();
        if ($request->image) {
            $dir = "storage/videos/";
            $image =  $this->uploadImage($request->image, $dir);
            $video->image = $image;
            $video->save();
        }
        toastr()->success('Video updated successfully');
        return back();
    }
    public function videoDestroy($id){
        $video = Video::find($id);
        $video->delete();
        toastr()->success('Video deleted successfully');
        return back();
    }

}
