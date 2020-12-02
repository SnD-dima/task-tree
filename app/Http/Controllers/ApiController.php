<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchInTree(Request $request)
    {
        $result = [];

        $tree = Tree::where('name','like',$request->name.'%')->get();

        foreach ($tree as $k => $v) {
            if(!$v->isRoot()) {
                $result[$k]           = Tree::ancestorsAndSelf($v)->toTree();
                $result[$k][0]['id']  = $v->id;
            }

        }
        if(empty($result)){
            return response()->json([],404);
        }
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItem(Request $request)
    {
        $node = $request->node;

        if($node) {
           $element = Tree::find($node['id']);
           $element->delete();
        }
        return response()->json([]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePosition(Request $request)
    {
        $child  = $request->child;
        $parent = $request->parent;

        if($child && $parent) {
            $element = Tree::find($child['id']);
            $element->parent_id = $parent['id'];
            $element->save();

        }
        return response()->json([]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTreeById(Request $request)
    {

        $element = Tree::find($request->id);
        $element = Tree::descendantsOf($element)->last();

        $result = Tree::ancestorsAndSelf($element)->toTree();

        return response()->json($result);

    }
}
