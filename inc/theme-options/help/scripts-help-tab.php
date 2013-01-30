<p><?php _e( 'This section goes to the footer of your site, so you would typically use it for any javascript snippets, e.g. a Google Analytics tracking code: ', 'meetup' ); ?></p>
<pre>&lt;script&gt;
var _gaq = [['_setAccount', 'UA-XXXXXXX-Y'], ['_trackPageview']];
(function(d, t) {
var g = d.createElement(t),
s = d.getElementsByTagName(t)[0];
g.async = true;
g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
s.parentNode.insertBefore(g, s);
})(document, 'script');
&lt;/script&gt;</pre>
<p><?php printf( __( 'If you add jQuery functions here, remember to use %1$s, like so: ', 'meetup' ), '<a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script#jQuery_noConflict_wrappers" target="_blank">' . __( 'jQuery noConflict wrappers', 'meetup' ) . '</a>' ); ?></p>
<pre>&lt;script&gt;
jQuery(document).ready(function($) {
    // $() will work as an alias for jQuery() inside of this function
});
&lt;/script&gt;</pre>
<p><?php _e( "The the main jQuery script shipped with WordPress gets loaded by default, so you can start adding functions here right away.", 'meetup' ); ?></p>