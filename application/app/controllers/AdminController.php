<?php

class AdminController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		$settings = Setting::first();

		$data = array(
			'settings' => $settings,
			);

		return View::make('admin.index', $data);
	}

	public function dashboard(){

		$settings = Setting::first();
		$media = Media::all();
		$users = User::all();
		$comments = Comment::all();
		$categories = Category::all();

		$data = array(
			'settings' => $settings,
			'media' => $media,
			'users' => $users,
			'comments' => $comments,
			'categories' => $categories,
			);

		return View::make('admin.sections.dashboard', $data);

	}

	public function themes(){

		$available_themes = Themes::get_themes();
		$active_theme = Setting::first()->theme;

		if(empty($active_theme)){ $active_theme = 'default'; }
		
		$data = array(
			'themes' => $available_themes,
			'active_theme' => $active_theme
			);

		return View::make('admin.sections.themes', $data);
	}

	public function media()
	{
		if(isset($_GET['sort'])){
			if(!isset($_GET['order'])){
				$_GET['order'] = 'ASC';
			}
			$media = Media::orderBy($_GET['sort'], $_GET['order'])->paginate(Config::get('site.num_results_per_page'));
		} else {
			$media = Media::orderBy('created_at', 'DESC')->paginate(Config::get('site.num_results_per_page'));
		}
		
		$data = array(
			'media' => $media,
			);
		return View::make('admin.sections.media', $data);
	}

	public function comments()
	{
		//$media = MediaFlag::groupBy('media_id')->get();
		$comments = Comment::all();
		$data = array(
			'comments' => $comments,
			);
		return View::make('admin.sections.comments', $data);
	}

	public function plugins()
	{
		$plugins_in_folder = Plugins::get_plugins();
		$plugins = Plugin::where('active', '=', 1)->get();
		$data = array(
			'plugins' => $plugins,
			'plugins_available' => $plugins_in_folder
			);
		return View::make('admin.sections.plugins', $data);
	}

	public function plugin_activate($slug){
		
		$plugin = Plugin::where('slug', '=', $slug)->first();
		$plugin_data = Plugins::get_plugin($slug);
		if(!empty($plugin->slug)){
			$plugin->active = 1;
			$plugin->save();
		} else {
			$new_plugin = new Plugin();
			$new_plugin->name = $plugin_data['name'];
			$new_plugin->description = $plugin_data['description'];
			$new_plugin->version = $plugin_data['version'];
			$new_plugin->slug = $slug;
			$new_plugin->active = 1;
			$new_plugin->save();
		}

		return Redirect::to('admin#plugins')->with(array('note' => 'Successfully Activated ' . $plugin_data['name'], 'note_type' => 'success'));
	}

	public function activate_theme($slug){
		$settings = Setting::first();
		$settings->theme = $slug;
		$settings->save();

		return Redirect::to('admin#themes')->with(array('note' => 'Successfully Activated ' . ucfirst($slug) . ' Theme', 'note_type' => 'success'));
	}

	public function plugin_deactivate($slug){
		$plugin_data = Plugins::get_plugin($slug);
		$plugin = Plugin::where('slug', '=', $slug)->first();
		$plugin->active = 0;
		$plugin->save();

		return Redirect::to('admin#plugins')->with(array('note' => 'Successfully De-activated ' . $plugin_data['name'], 'note_type' => 'success'));
	}

	public function users()
	{
		$users = User::all();
		$data = array(
			'users' => $users,
			);
		return View::make('admin.sections.users', $data);
	}

	public function update()
	{
		$users = User::all();
		$data = array(
			'users' => $users,
			);
		return View::make('admin.sections.update', $data);
	}

	public function categories()
	{
		$categories = Category::orderBy('order', 'ASC')->get();
		$data = array(
			'categories' => $categories,
			);
		return View::make('admin.sections.categories', $data);
	}

	public function settings()
	{
		$settings = Setting::first();
		$data = array(
			'settings' => $settings,
			);
		return View::make('admin.sections.settings', $data);
	}

	public function update_settings()
	{
		$input = Input::all();
		$settings = Setting::first();



		if(isset($input['auto_approve_posts'])){
			$input['auto_approve_posts'] = 1;
		} else {
			$input['auto_approve_posts'] = 0;
		}

		$settings->auto_approve_posts = htmlspecialchars($input['auto_approve_posts']);

		if(isset($input['user_registration'])){
			$input['user_registration'] = 1;
		} else {
			$input['user_registration'] = 0;
		}

		$settings->user_registration = htmlspecialchars($input['user_registration']);

		if(isset($input['infinite_scroll'])){
			$input['infinite_scroll'] = 1;
		} else {
			$input['infinite_scroll'] = 0;
		}

		if(isset($input['random_bar_enabled'])){
			$input['random_bar_enabled'] = 1;
		} else {
			$input['random_bar_enabled'] = 0;
		}


		if(isset($input['media_description'])){
			$input['media_description'] = 1;
		} else {
			$input['media_description'] = 0;
		}


		if(isset($input['enable_watermark'])){
			$input['enable_watermark'] = 1;
		} else {
			$input['enable_watermark'] = 0;
		}

		if(isset($input['pages_in_menu'])){
			$input['pages_in_menu'] = 1;
		} else {
			$input['pages_in_menu'] = 0;
		}

		if(isset($input['infinite_load_btn'])){
			$input['infinite_load_btn'] = 1;
		} else {
			$input['infinite_load_btn'] = 0;
		}

		if(isset($input['captcha'])){
			$input['captcha'] = 1;
		} else {
			$input['captcha'] = 0;
		}

		$settings->captcha = htmlspecialchars($input['captcha']);
		$settings->captcha_public_key = htmlspecialchars($input['captcha_public_key']);
		$settings->captcha_private_key = htmlspecialchars($input['captcha_private_key']);


		$settings->pages_in_menu = htmlspecialchars($input['pages_in_menu']);
		$settings->pages_in_menu_text = htmlspecialchars($input['pages_in_menu_text']);
		$settings->infinite_load_btn = htmlspecialchars($input['infinite_load_btn']);

		$settings->enable_watermark = htmlspecialchars($input['enable_watermark']);

		if(Input::hasFile('watermark_image')){
			$settings->watermark_image = ImageHandler::uploadImage(Input::file('watermark_image'), 'settings');
		}

		$settings->watermark_position = htmlspecialchars($input['watermark_position']);
		$settings->watermark_offset_x = htmlspecialchars($input['watermark_offset_x']);
		$settings->watermark_offset_y = htmlspecialchars($input['watermark_offset_y']);

		$settings->infinite_scroll = htmlspecialchars($input['infinite_scroll']);
		$settings->random_bar_enabled = htmlspecialchars($input['random_bar_enabled']);
		$settings->media_description = htmlspecialchars($input['media_description']);

		if(Input::hasFile('logo')){
			$settings->logo = ImageHandler::uploadImage(Input::file('logo'), 'settings');
		}

		if(Input::hasFile('favicon')){
			$settings->favicon = ImageHandler::uploadImage(Input::file('favicon'), 'settings');
		}

		$settings->website_name = htmlspecialchars($input['website_name']);
		$settings->website_description = htmlspecialchars($input['website_description']);

		$settings->primary_color = htmlspecialchars($input['primary_color']);
		$settings->secondary_color = htmlspecialchars($input['secondary_color']);
		$settings->like_icon = htmlspecialchars($input['like_icon']);

		$settings->fb_key = htmlspecialchars($input['fb_key']);
		$settings->fb_secret_key = htmlspecialchars($input['fb_secret_key']);
		$settings->facebook_page_id = htmlspecialchars($input['facebook_page_id']);

		$settings->google_key = htmlspecialchars($input['google_key']);
		$settings->google_secret_key = htmlspecialchars($input['google_secret_key']);
		$settings->google_page_id = htmlspecialchars($input['google_page_id']);

		$settings->twitter_page_id = htmlspecialchars($input['twitter_page_id']);

		$settings->analytics = stripslashes($input['analytics']);

		$settings->system_email = $input['system_email'];

		$settings->save();

		$data = array(
			'settings' => $settings,
			);



		$notification_text = Lang::get('lang.update_settings_success');

		return Redirect::to('admin#settings')->with(array('note' => $notification_text, 'note_type' => 'success'));
	}

	public function flagged_answers()
	{
		$answers = CommentFlag::groupBy('comment_id')->get();
		$data = array(
			'flagged_answers' => $answers,
			);
		return View::make('admin.sections.flagged_answers', $data);
	}

	public function flagged_users()
	{
		$users = UserFlag::groupBy('user_flagged_id')->get();
		$data = array(
			'flagged_users' => $users,
			);
		return View::make('admin.sections.flagged_users', $data);
	}

	public function custom_code(){
		$data = array('custom_css' => Setting::first()->custom_css, 'custom_js' => Setting::first()->custom_js);
		return View::make('admin.sections.custom_code', $data);
	}

	public function ad_placements(){
		$data = array('square_ad' => Setting::first()->square_ad);
		return View::make('admin.sections.ad_placements', $data);
	}

	public function update_ad_placements(){
		$ads = Input::all();

		$settings = Setting::first();
		$settings->square_ad = $ads['square_ad'];
		
		if($settings->save()){
			return 'success';
		} else {
			return 'fail';
		}

	}

	public function update_custom_css(){
		$css = Input::get('css');
		$settings = Setting::first();
		$settings->custom_css = $css;
		$settings->save();
		return $settings;
	}

	public function update_custom_js(){
		$js = Input::get('js');
		$settings = Setting::first();
		$settings->custom_js = $js;
		$settings->save();
		return $settings;
	}

	public function deactivate_user($id){
		$user = User::find($id);
		$user->active = 0;
		$user->save();
		
		return Redirect::to('admin#users')->with(array('note' => Lang::get('lang.deactivate_user'), 'note_type' => 'success') );
	}

	public function activate_user($id){
		$user = User::find($id);
		$user->active = 1;
		$user->save();
		
		return Redirect::to('admin#users')->with(array('note' => Lang::get('lang.activate_user'), 'note_type' => 'success') );
	}

	public function pages()
	{
		$pages = Page::orderBy('order', 'ASC')->get();
		$data = array(
			'pages' => $pages,
			);
		return View::make('admin.sections.pages', $data);
	}

	public function create_pages(){
		$input = Input::all();

		if(isset($input['active'])){
			$input['active'] = 1;
		} else {
			$input['active'] = 0;
		}

		if(isset($input['show_in_menu'])){
			$input['show_in_menu'] = 1;
		} else {
			$input['show_in_menu'] = 0;
		}

		$page = Page::create(array('title' => $input['title'], 'url' => $input['url'], 'body' => stripslashes($input['body']), 'order' => $input['order'], 'active' => $input['active'], 'show_in_menu' => $input['show_in_menu']));
	
		return Redirect::to('admin#pages')->with(array('note' => 'Successfully Create New Page', 'note_type' => 'success'));

	}

	public function delete_pages($id){
		$page = Page::find($id);

		$page->delete();

		return Redirect::to('admin#pages')->with(array('note' => 'successfully deleted page', 'note_type' => 'success'));
	}

	public function update_pages($id){
		

		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Page::$rules);

		if ($validation->passes())
		{
			$page = Page::find($id);

			if(isset($input['active'])){
				$input['active'] = 1;
			} else {
				$input['active'] = 0;
			}

			if(isset($input['show_in_menu'])){
				$input['show_in_menu'] = 1;
			} else {
				$input['show_in_menu'] = 0;
			}
			
			$input['body'] = stripslashes($input['body']); 

			$page->update($input);

			return Redirect::to('admin#pages')->with(array('note' => 'successfully updated page', 'note_type' => 'success'));
		}

		return Redirect::to('admin#pages', $id)
			->withInput()
			->withErrors($validation)
			->with('message', Lang::get('lang.validation_errors'));
	}
	

	public function toggle_active($id){
		$media = Media::find($id);
		if($media->active == 1){
			$media->active = 0;
		} else {
			$media->active = 1;
		}
		$media->save();
	}

}