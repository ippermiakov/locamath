/**
 * CoolInput Plugin
 *
 * @version 1.4 (05/09/2009)
 * @requires jQuery v1.2.6+
 * @author Alex Weber <alexweber.com.br>
 * @copyright Copyright (c) 2008-2009, Alex Weber
 * @see http://remysharp.com/2007/01/25/jquery-tutorial-text-box-hints/
 *
 * Distributed under the terms of the GNU General Public License
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
(function(a){a.fn.coolinput=function(b){var c={hint:null,source:"title",blurClass:"blur",iconClass:false,clearOnSubmit:true};if(b&&typeof b=="object"){a.extend(c,b)}else{c.hint=b}return this.each(function(){var d=a(this);var e=c.hint||d.attr(c.source);if(e){d.blur(function(){if(d.val()==""){d.val(e).addClass(c.blurClass)}}).focus(function(){if(d.val()==e){d.val("").removeClass(c.blurClass)}});if(c.clearOnSubmit){d.parents("form:first").submit(function(){if(d.hasClass(c.blurClass)){d.val("")}})}if(c.iconClass){d.addClass(c.iconClass)}d.blur()}})}})(jQuery);
