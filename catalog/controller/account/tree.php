<?php

require_once(DIR_SYSTEM.'laravel/load.php');

use App\Eloquent\Customer;
use App\Eloquent\Ntree;
use App\Eloquent\Btree;
use App\Eloquent\Encapsulator;

class ControllerAccountTree extends Controller
{
	public function index() {

		$this->document->setTitle($this->language->get('PV'));
		$this->document->addScript('catalog/view/javascript/jquery/jstree/jstree.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/jstree/themes/default/style.min.css');

		Encapsulator::init();

// 		$customer = Customer::find(68);
// 		$customer->passNtreeBonus(100);
// return;
		// $customer = Customer::find($this->customer->getId());
		$customer = Customer::find(2);
		// $root = Btree::create(['name' => 'Root category']);
		// $root = Btree::find(1);
		// $root->customer()->associate($customer);
		// $root->save();

		// $child = Btree::find(13);
		// $child->delete();

		// $child = Customer::find(69);
		// $customer->addBtreeChild($child);
		// return;

		$data['profit_histories'] = $customer->pv_histories()->get();

		$ntree_descendants = $customer->ntree->descendantsAndSelf()->with('customer')->get()->toHierarchy();

		$ntree_content = $this->treeHelper($ntree_descendants);
		$data['ntree'] = $ntree_content;

		$btree_descendants = $customer->btree->descendantsAndSelf()->with('customer')->get()->toHierarchy();

		$btree_content = $this->treeHelper($btree_descendants);
		$data['btree'] = $btree_content;

		// foreach ($descendants as $descendant) {
		// 	foreach ($descendant->children as $child) {
		// 		print_r($child->customer);
		// 	}
		// }
		// print_r($customer->ntree->getDescendantsAndSelf()->toHierarchy());

// $app = new Illuminate\Container\Container();
// $app['env'] = 'production';
// $s = new BaumServiceProvider($app);
		// Tree::rebuild(true);
		// $root = Ntree::create(['name' => 'Root category']);
		// $root->setDefaultLeftAndRight();
		// $root->makeRoot();
		// $root = Ntree::find(9);
		// $root->makeRoot();
		// print_r($root->getDescendantsAndSelf()->toHierarchy());
		// $child2 = Ntree::create(['name' => 'Son 1']);
		// $child2->save();
		// $child = Tree::find(129);
		// $child2 = Tree::find(128);
		// $child->moveToLeftOf($child2);
		// $child2->makeChildOf($root);
		// $child1 = $root->children()->create(['name' => 'Child 4']);
		// print_r($root->getDescendantsAndSelf()->toHierarchy());

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/tree.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/tree.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/tree.tpl', $data));
		}
	}

	public function treeHelper($descendants) {
		$content = '<ul>';
		foreach ($descendants as $descendant) {
			$content .= '<li class="jstree-open" data-jstree=\'{"icon":"glyphicon glyphicon-leaf"}\'>'.$descendant->customer->customer_id.'&nbsp;'.$descendant->customer->firstname.'&nbsp;'.$descendant->customer->lastname.'&nbsp;個人PV:'.$descendant->customer->pv.'&nbsp;個人組織:'.$descendant->customer->total_pv;
			if ($descendant->children->count() > 0) {
				$content .= $this->treeHelper($descendant->children);
			}
			$content .= '</li>';
		}
		$content .= '</ul>';
		return $content;
	}
}