<h1> “Otwarte Zabytki Digitalizacja” </h1>
portal was created to digitize Opole state's monuments and related documents for CenrumCyfrowe.pl. It's complementary to OtwarteZabytki.pl.
Latest running version can be found at http://wuoz.otwartezabytki.pl. 
The portal is Wordpress-Based and is published under CC BY-SA 3.0. 
It can be used as a Wordpress theme – it does not require any additional plugins to run. 

<h2> Theme's main features: </h2>

<p>User-wise:</p>
- present monuments on a map
- browse documents and monuments using types and keyword
- provide open access to digitized (scanned) documents
- allow logged users to collect and group their favorite objects into folders

<p>Admin-wise:</p>
- Organize documents and documents within one relative database via Wordpress CMS,
- xml-based batch import of scanned documents previously uploaded to ftp server, 

<h2>Frameworks and technologies:</h2>
- Zurb Foundation 4 with  - http://foundation.zurb.com/ 
- Compass, Grunt with watch and subtle css reload – http://compass-style.org/, http://gruntjs.com/ 
- Google Maps for map representation – http://maps.google.com 
- Gmap3 for Google Maps api - http://gmap3.net/ 
- hybridoauth for facebook, twitter, google+ login - http://hybridauth.sourceforge.net/ 
- WheelZoom for picture zooming - http://www.jacklmoore.com/wheelzoom/ 
- jcarousel for picture browsing - http://sorgalla.com/jcarousel/ 

<h3>File Structure (use “grunt dist” for production theme): </h3>

- apple_touch // apple touch icons
- functions // extensions of functions.php
- grunt // grunt configuration with no npm modules – they have to be downloaded
- img // non-svg images files
- javascripts // js files that compile into one scripts.min.js via grunt:dev
- libraries // additional php libraries
- options // admin options, and theme options
- partials // php template files 
- post-types // admin post type registration
- sccss // scss dev files
- single // document and monument single views
- stylesheets // generated css files
- svg // svg image files, icons

<h3> Possible use cases: </h3>
- portals with searchable objects that can be connected with one-to-many relation,
- Simple portal where logged users are able create their own collection via friendly drag and drop and clipboard system from the objects within the portal
- present objects on map and attach digital resources via Wordpress CMS, i.e. places and photos


<h3> WARNING: </h3>
- the theme was never meant to be a an multi-purpose theme
- most of the solutions are dedicated, although some configuration is available through theme options

<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>

<h4>Founded by the The Ministry of Culture and National Heritage http://www.mkidn.gov.pl/</h4>

Contributors: 
- http://otwartezabytki.pl
- http://centrumcyfrowe.pl/
- http://www.wuozopole.pl/
- http://vividstudio.pl
- http://webchefs.pl




