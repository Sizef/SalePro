<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Story;
use App\StoryImages;
use DB;
use Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class StoryImageController extends Controller
{
    public function index()
    {
        $lims_stories = Story::where('is_active', true)->get();
        return view('stories.story-items', compact('lims_stories'));
    }

    public function storyData(Request $request)
    {
        $columns = array( 
            0 =>'id',
            2 =>'image',
            3 =>'link',
            4=> 'story_id',
            5=> 'is_active',
        );
        
        $totalData = StoryImages::where('is_active', true)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
            $story_images = StoryImages::offset($start)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        else
        {
            $search = $request->input('search.value'); 
            $story_images =  StoryImages::where([
                            ['name', 'LIKE', "%{$search}%"],
                            ['is_active', true]
                        ])->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = StoryImages::where([
                            ['name','LIKE',"%{$search}%"],
                            ['is_active', true]
                        ])->count();
        }
        $data = array();
        if(!empty($story_images))
        {
            foreach ($story_images as $key=>$item)
            {
                $nestedData['id'] = $item->id;
                $nestedData['key'] = $key;

                $nestedData['image'] = '<img src="'.url('stories', $item->image).'" height="80" width="80">';
                $nestedData['link'] = $item->link;

                $story = Story::where('id',$item->story_id)->where('is_active',true)->first();

                $nestedData['story'] = $story->name;

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="'.$item->id.'" class="edit-story-image btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i> '.trans("file.edit").'</button>
                                </li>
                                <li class="divider"></li>'.
                                \Form::open(["route" => ["storyImage.destroy", $item->id], "method" => "DELETE"] ).'
                                <li>
                                  <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button> 
                                </li>'.\Form::close().'
                            </ul>
                        </div>';
                $data[] = $nestedData;
            }
        }
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data);
    }

    public function store(Request $request)
    {
        $request->name = preg_replace('/\s+/', ' ', $request->name);
        $this->validate($request, [
            'image' => 'image|mimes:jpg,jpeg,png',
        ]);
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis");
            $imageName = $imageName . '.' . $ext;
            $image->move('stories', $imageName);

            $img_lg = Image::make('stories/'. $imageName)->fit(233, 414)->save();
            
            $lims_story_image_data['image'] = $imageName;
        }
        $lims_story_image_data['link'] = $request->link;
        $lims_story_image_data['story_id'] = $request->story_id;
        StoryImages::create($lims_story_image_data);
        return redirect('story-images')->with('message', 'Data inserted successfully');
    }

    public function edit($id)
    {
        $lims_story_image_data = StoryImages::findOrFail($id);
        return $lims_story_image_data;
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'image' => 'image|mimes:jpg,jpeg,png,gif',
        ]);

        $input = $request->except('image');

        //return $input;
        $lims_story_image_data = StoryImages::findOrFail($request->story_image_id);
        $lims_story_image_data->update($input);
        return redirect('story-images')->with('message', 'Data updated successfully');
    }
    
    public function destroy($id)
    {
        $lims_story_image_data = StoryImages::findOrFail($id);
        $lims_story_image_data->is_active = false;
        $lims_story_image_data->save();
        return redirect('story-images')->with('not_permitted', 'Data deleted successfully');
    }
}
