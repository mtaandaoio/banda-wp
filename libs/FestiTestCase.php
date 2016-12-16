<?php

class FestiTestCase extends WP_UnitTestCase
{
    protected function setMainPage(WP_Post $page)
    {
        update_option('page_on_front', $page->ID);
        update_option('show_on_front', 'page');
    } // end setMainPage
    
    protected function doAction($name)
    {
        ob_start();
        
        do_action($name);
        
        return ob_get_clean();
    } // end doAction
    
    protected function createPage($options = array())
    {
        $options['post_type'] = 'page';
        
        return $this->createPost($options);
    } // end createPage
    
    protected function createPost(&$options)
    {
        if (empty($options['post_title'])) {
            $options['post_title'] = 'content_'.rand(1000, 100000);
        }
        
        if (empty($options['post_type'])) {
            $options['post_type'] = 'post';
        }
        
        return self::factory()->post->create_and_get($options);
    } // end createPost 
    
    
    //$content = apply_filters('the_content', $page->post_content);
    //get_post_field('post_content', $page->ID);
    //echo $content;
    
}