Ext.data.JsonP.siesta_automation({"guide":"<h2 id='siesta_automation-section-intro'>Intro</h2>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/siesta_automation-section-intro'>Intro</a></li>\n<li><a href='#!/guide/siesta_automation-section-running-tests-using-selenium-webdriver'>Running tests using Selenium WebDriver</a></li>\n<li><a href='#!/guide/siesta_automation-section-running-tests-using-slimerjs'>Running tests using SlimerJS</a></li>\n<li><a href='#!/guide/siesta_automation-section-running-tests-using-remote-selenium-webdriver-server'>Running tests using remote Selenium WebDriver server</a></li>\n<li><a href='#!/guide/siesta_automation-section-running-tests-in-phantomjs'>Running tests in PhantomJS</a></li>\n<li><a href='#!/guide/siesta_automation-section-parallelization'>Parallelization</a></li>\n<li><a href='#!/guide/siesta_automation-section-reporting-the-results-of-a-test-suite-execution-in-a-structured-format.'>Reporting the results of a test suite execution in a structured format.</a></li>\n<li><a href='#!/guide/siesta_automation-section-exit-codes'>Exit codes</a></li>\n<li><a href='#!/guide/siesta_automation-section-buy-this-product'>Buy this product</a></li>\n<li><a href='#!/guide/siesta_automation-section-support'>Support</a></li>\n<li><a href='#!/guide/siesta_automation-section-see-also'>See also</a></li>\n<li><a href='#!/guide/siesta_automation-section-copyright-and-license'>COPYRIGHT AND LICENSE</a></li>\n</ol>\n</div>\n\n<p>Running the test suite manually in browsers is very convenient as it allows you to easily debug your tests. However, when\nsetting up a continuous integration process, it quickly becomes time-consuming - ideally you should to run the test suite\nin each and every browser after (or before) each and every commit.</p>\n\n<p>This guide describes how you can automate the launching of a Siesta test suite.</p>\n\n<p><strong>Please note:</strong> This functionality is only available in the Siesta Standard package.</p>\n\n<h2 id='siesta_automation-section-running-tests-using-selenium-webdriver'>Running tests using Selenium WebDriver</h2>\n\n<p><strong>Important</strong>. Using the WebDriver requires some manual configuration steps for IE and Safari. Please refer to these pages <a href=\"http://code.google.com/p/selenium/wiki/InternetExplorerDriver\">http://code.google.com/p/selenium/wiki/InternetExplorerDriver</a> and\n<a href=\"http://code.google.com/p/selenium/wiki/SafariDriver\">http://code.google.com/p/selenium/wiki/SafariDriver</a>. Also, when running test suite in IE, make sure the IE window is focused (on top of other windows) and\nmouse cursor is outside of the IE window.</p>\n\n<p>You can automate the launching of your test suite in several browsers, using Selenium WebDriver. Unlike SlimerJS, it requires the presence of actual browsers in the operation system.</p>\n\n<p>On MacOS and Linux:</p>\n\n<pre><code>&gt; __SIESTA__/bin/webdriver http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>On Windows:</p>\n\n<pre><code>&gt; __SIESTA__\\bin\\webdriver http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>Here, the <code>__SIESTA__</code> placeholder is the path to your Siesta package. The launcher script <code>bin/webdriver</code> accepts the URL to your html wrapper for the Siesta harness (<code>index.html</code>)\nand several optional options.</p>\n\n<p>All options should start with double minus, e.g: <code>--browser chrome</code> or <code>--browser=chrome</code>. Most important options (see bin/webdriver --help for all):</p>\n\n<ul>\n<li><code>help</code> - prints help message with all available options</li>\n<li><code>browser browsername</code> - can be one of \"firefox / chrome / ie / safari\"'.</li>\n<li><code>max-workers</code> - maximum number of parallel testing \"threads\" that can be opened simultaneously. <strong>Note</strong>, that if your tests involves focusing\nof the DOM elements (as most UI tests do) then this option should be probably kept at value 1, unless you are using a cloud-based\ntesting infrastructure. See also a \"Parallelization\" section below.</li>\n<li><code>include regexp</code>      - a regexp to filter the urls of tests. When provided, only the tests with urls matching this filter be executed</li>\n<li><code>verbose</code> - will include the information about every individual assertions to the output. By default, only failed assertions will be shown</li>\n<li><code>debug</code> - will enable various debugging messages</li>\n<li><code>report-format</code> - the format of the report, see the \"Reporting the results of a test suite execution in a structured format\" section below</li>\n<li><code>report-file</code> - the file to save the report to</li>\n</ul>\n\n\n<p>In case of any failures in the test suite the command will exit with non-zero exit code. See \"Exit codes\" section for details.</p>\n\n<h2 id='siesta_automation-section-running-tests-using-slimerjs'>Running tests using SlimerJS</h2>\n\n<p>SlimerJS is a semi-headless FireFox browser. It is called \"semi-headless\" because it requires the <code>xvfb</code> utility to run on systems w/o\ngraphical environment. In fact, even on systems with graphical environment, we also recommend to use <code>xvfb</code> to isolate Slimer launcher\nfrom the other windows, so that it will have exclusive focus.</p>\n\n<p>SlimerJS uses real rendering engine of the Firefox browser - you can trust the results from SlimerJS as if they were received from \"real\" Firefox.</p>\n\n<p>SlimerJS is now a recommended headless launcher for Siesta.</p>\n\n<p>On MacOS and Linux:</p>\n\n<pre><code>&gt; __SIESTA__/bin/slimerjs http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>On Windows:</p>\n\n<pre><code>&gt; __SIESTA__\\bin\\slimerjs http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>Here, the <code>__SIESTA__</code> placeholder is the path to your Siesta package. The launcher script <code>bin/webdriver</code> accepts the URL to your html wrapper for the Siesta harness (<code>index.html</code>)\nand several optional options.</p>\n\n<p>All options should start with double minus, e.g: <code>--browser chrome</code> or <code>--browser=chrome</code>. Most important options (see bin/webdriver --help for all):</p>\n\n<ul>\n<li><code>help</code> - prints help message with all available options</li>\n<li><code>browser browsername</code> - can be one of \"firefox / chrome / ie / safari\"'.</li>\n<li><code>max-workers</code> - maximum number of parallel testing \"threads\" that can be opened simultaneously. <strong>Note</strong>, that if your tests involves focusing\nof the DOM elements (as most UI tests do) then this option should be probably kept at value 1, unless you are using a cloud-based\ntesting infrastructure. See also a \"Parallelization\" section below.</li>\n<li><code>include regexp</code>      - a regexp to filter the urls of tests. When provided, only the tests with urls matching this filter be executed</li>\n<li><code>verbose</code> - will include the information about every individual assertions to the output. By default, only failed assertions will be shown</li>\n<li><code>debug</code> - will enable various debugging messages</li>\n<li><code>report-format</code> - the format of the report, see the \"Reporting the results of a test suite execution in a structured format\" section below</li>\n<li><code>report-file</code> - the file to save the report to</li>\n</ul>\n\n\n<p>In case of any failures in the test suite the command will exit with non-zero exit code. See \"Exit codes\" section for details.</p>\n\n<h2 id='siesta_automation-section-running-tests-using-remote-selenium-webdriver-server'>Running tests using remote Selenium WebDriver server</h2>\n\n<p>You can launch the test suite (and receive the results) on one machine, but physically open the browsers on some other machine using RemoteWebDriver server.\nTo do that, first start the RemoteWebDriver server on target machine:</p>\n\n<p>On MacOS and Linux:</p>\n\n<pre><code>&gt; __SIESTA__/bin/webdriver-server [OPTIONS]\n</code></pre>\n\n<p>On Windows:</p>\n\n<pre><code>&gt; __SIESTA__\\bin\\webdriver-server [OPTIONS]\n</code></pre>\n\n<p>\"webdriver-server\" is a very thin wrapper around \"selenium-server-standalone-xx.jar\" which just specifies the location of binaries for various browsers. It bypass\nany command line options to that jar file. For example, to specify the port of server (default value is 4444), specify it as:</p>\n\n<pre><code>&gt; __SIESTA__/bin/webdriver-server -port 4444\n</code></pre>\n\n<p>For a list of available options for server, launch it with \"--help\" switch. Also, please refer to: <a href=\"http://code.google.com/p/selenium/wiki/RemoteWebDriverServer\">http://code.google.com/p/selenium/wiki/RemoteWebDriverServer</a></p>\n\n<p>Then, when launching the test suite with \"webdriver\" launcher, specify an additional \"--host\" option:</p>\n\n<pre><code>&gt; __SIESTA__/bin/webdriver http://my.harness.url/tests --host remote.webdriver.host --port 4444\n</code></pre>\n\n<p>You can use the \"--cap\" switch to specify various Selenium capabilities for example \"--cap browserName=firefox --cap platform=XP\",\nsee <a href=\"https://code.google.com/p/selenium/wiki/DesiredCapabilities\">https://code.google.com/p/selenium/wiki/DesiredCapabilities</a></p>\n\n<h2 id='siesta_automation-section-running-tests-in-phantomjs'>Running tests in PhantomJS</h2>\n\n<p><strong>Important</strong> It seems, the PhantomJS project is no longer actively maintained (the last release with several major known issues\nmade Jan 23 2015). Plus, the architecture of the project is not ideal. Instead of including real rendering engine of some browser\n(for example as SlimerJS does), Phantom is based on some snapshot of the WebKit project, that is used in the Qt framework.\nWe recommend you to use PhantomJS only for quick non-DOM testing, and use Selenium WebDriver / SlimerJS launchers for UI testing.</p>\n\n<p>PhantomJS allows you to run your tests in a headless Webkit browser. It's quite suitable for Linux servers w/o any graphical interface or browsers installed.</p>\n\n<p><p><img src=\"guides/siesta_automation/images/phantomjs.png\" alt=\"\" width=\"836\" height=\"309\"></p></p>\n\n<p>To launch the test suite in PhantomJS, run the following command.</p>\n\n<p>On MacOS and Linux:</p>\n\n<pre><code>&gt; __SIESTA__/bin/phantomjs http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>On Windows:</p>\n\n<pre><code>&gt; __SIESTA__\\bin\\phantomjs http://yourproject/tests/index.html [OPTIONS]\n</code></pre>\n\n<p>Here, the <code>__SIESTA__</code> placeholder is the path to your siesta package. The launch script <code>bin/phantomjs</code> accepts 2 arguments - the URL to your html wrapper for the Siesta harness (<code>index.html</code>)\nand an several optional options.</p>\n\n<p>All options should start with double minus, e.g: <code>--report-format JSON</code> or <code>--report-format=JSON</code>. Most important options (see bin/phantomjs --help for all):</p>\n\n<ul>\n<li><code>help</code> - prints help message with all available options</li>\n<li><code>max-workers</code> - maximum number of parallel testing \"threads\" that can be opened simultaneously. <strong>Note</strong>, that if your tests involves focusing\nof the DOM elements (as most UI tests do) then this option should be probably kept at value 1 (unless you are using cloud-based infrastructure)</li>\n<li><code>include regexp</code>      - a regexp. When provided, only the tests with matching urls will be executed. This option has an alias - \"filter\"</li>\n<li><code>exclude regexp</code>      - a regexp. When provided, the tests with matching urls will not be executed.</li>\n<li><code>verbose</code> - will include the information about every individual assertions to the output. By default, only failed assertions will be shown</li>\n<li><code>report-format</code> - the format of the report, see the \"Reporting the results of a test suite execution in a structured format\" section below</li>\n<li><code>report-file</code> - the file to save the report to</li>\n</ul>\n\n\n<p>In case of any failures in the test suite, the command will exit with a non-zero exit code. See \"Exit codes\" section for details.</p>\n\n<h2 id='siesta_automation-section-parallelization'>Parallelization</h2>\n\n<p>When using cloud-based infrastructure, each test page is running inside of the own VM, which guarantees the exclusive focus owning\nand allows us to run several test pages in parallel. Naturally, that speed ups the test execution, by the number of parallel sessions\nwe can run.</p>\n\n<p>This can be done using the <code>--max-workers</code> option, that specifies the maximum number of parallel sessions.</p>\n\n<p><strong>Important</strong>. When value of this option is more than 1, the order of tests execution is not defined. A test, that goes lower\nin the <code>harness.start()</code> list, can be executed before the test above it. This is simply because all tests are divided in several\n\"threads\" and all threads are executed simultaneously. You should not rely on some test being run after another, instead,\nevery test should execute standalone (allocate exclusive resources, perform all necessary setup).</p>\n\n<h2 id='siesta_automation-section-reporting-the-results-of-a-test-suite-execution-in-a-structured-format.'>Reporting the results of a test suite execution in a structured format.</h2>\n\n<p>You can easily export the results of a test suite execution in the structured format. To do that, provide the <code>--report-format</code> option to the PhantomJS or Selenium launcher.\nWhen providing this option for phantomjs you also need to provide the <code>--report-file</code> option, indicating the filename of the report to be written.</p>\n\n<p>Currently the only supported formats are \"JSON\" and \"JUnit\". Using the JSON option looks like this:</p>\n\n<pre><code>C:\\siesta\\phantomjs http://localhost/YourApplication/tests --report-format JSON --report-file foo.json\n</code></pre>\n\n<p>And this will generate a file named foo.json containing the following JSON structure:</p>\n\n<pre><code>{\n    \"testSuiteName\" : \"Siesta self-hosting test suite\",\n    \"startDate\"     : 1343114314723,\n    \"endDate\"       : 1343114315401,\n    \"passed\"        : true,\n    \"testCases\"     : [{\n        \"url\"           : \"010_sanity.t.js\",\n        \"startDate\"     : 1343114315390,\n        \"endDate\"       : 1343114315396,\n        \"passed\"        : true,\n        \"assertions\"    : [{\n            \"passed\"        : true,\n            \"description\"   : \"Siesta is here\",\n            \"type\"          : \"Siesta.Result.Assertion\"\n        }, {\n            \"passed\"        : true,\n            \"description\"   : \"<a href=\"#!/api/Siesta.Test\" rel=\"Siesta.Test\" class=\"docClass\">Siesta.Test</a> is here\",\n            \"type\"          : \"Siesta.Result.Assertion\"\n        }, {\n            \"passed\"        : true,\n            \"description\"   : \"<a href=\"#!/api/Siesta.Harness\" rel=\"Siesta.Harness\" class=\"docClass\">Siesta.Harness</a> is here\",\n            \"type\"          : \"Siesta.Result.Assertion\"\n        }, {\n            \"passed\"        : false,\n            \"description\"   : \"Field 1 focused\",\n            \"annotation\"    : \"Failed assertion `ok` at line 27 of keyevents/050_tab_key_focus2.t.js\\nGot: false\\nNeed \\\"truthy\\\" value\",\n            \"sourceLine\"    : \"27\",\n            \"name\"          : \"ok\"\n            \"type\"          : \"Siesta.Result.Assertion\"\n        }]\n    }]\n}\n</code></pre>\n\n<p>Using the JUnit report option looks like this:</p>\n\n<pre><code>C:\\siesta\\phantomjs http://localhost/YourApplication/tests --report-format JUnit --report-file foo.xml\n</code></pre>\n\n<p>This will generate a file named foo.xml containing the following JUnit XML structure:</p>\n\n<pre><code>&lt;testsuite errors=\"0\" failures=\"1\" hostname=\"localhost:8085\" name=\"Ext Scheduler Test Suite\" tests=\"2\" time=\"3.594\" timestamp=\"2012-06-06T08:55:21.520\"&gt;\n\n    &lt;testcase classname=\"Bryntum.Test\" name=\"lifecycle/040_schedulergrid.t.js\" time=\"1.238\"&gt;\n        &lt;failure message=\"Oops\" type=\"FAIL\"&gt;&lt;/failure&gt;\n    &lt;/testcase&gt;\n\n    &lt;testcase classname=\"Bryntum.Test\" name=\"lifecycle/042_schedulergrid_right_columns.t.js\" time=\"0.818\"&gt;&lt;/testcase&gt;\n&lt;/testsuite&gt;\n</code></pre>\n\n<p>When providing this option for Selenium you will also need to provide the <code>--report-file-prefix</code> option. It has a slightly different meaning compared to PhantomJS, since the Selenium launcher can run the\ntest suite in several browsers which generates several reports. These reports will be saved into different files, and the first part of the filename will be specified with the <code>report-file-prefix</code>\noption, and the browser name will also be included in the filename. The value for this option may have an extension, which will be preserved.</p>\n\n<p>For example, specifying: <code>--report-file-prefix=report_.json</code> will save the reports to: <code>report_firefox.json</code>, <code>report_ie.json</code>, etc.</p>\n\n<h2 id='siesta_automation-section-exit-codes'>Exit codes</h2>\n\n<ul>\n<li>0 - All tests passed successfully</li>\n<li>1 - Some tests failed</li>\n<li>3 - No supported browsers available on this machine</li>\n<li>4 - No tests to run (probably filter doesn't match any test url)</li>\n<li>5 - Can't open harness page</li>\n<li>6 - Wrong command line arguments</li>\n<li>8 - Exit after showing the Siesta version (when <code>--version</code> is provided on the command line)</li>\n</ul>\n\n\n<h2 id='siesta_automation-section-buy-this-product'>Buy this product</h2>\n\n<p>Visit our store: <a href=\"http://bryntum.com/store/siesta\">http://bryntum.com/store/siesta</a></p>\n\n<h2 id='siesta_automation-section-support'>Support</h2>\n\n<p>Ask a question in our community forum: <a href=\"http://www.bryntum.com/forum/viewforum.php?f=20\">http://www.bryntum.com/forum/viewforum.php?f=20</a></p>\n\n<p>Share your experience in our IRC channel: <a href=\"http://webchat.freenode.net/?randomnick=1&amp;channels=bryntum&amp;prompt=1\">#bryntum</a></p>\n\n<p>Please report any bugs through the web interface at <a href=\"https://www.assembla.com/spaces/bryntum/support/tickets\">https://www.assembla.com/spaces/bryntum/support/tickets</a></p>\n\n<h2 id='siesta_automation-section-see-also'>See also</h2>\n\n<p>Web page of this product: <a href=\"http://bryntum.com/products/siesta\">http://bryntum.com/products/siesta</a></p>\n\n<p>Other Bryntum products: <a href=\"http://bryntum.com/products\">http://bryntum.com/products</a></p>\n\n<h2 id='siesta_automation-section-copyright-and-license'>COPYRIGHT AND LICENSE</h2>\n\n<p>Copyright (c) 2009-2015, Bryntum AB &amp; Nickolay Platonov</p>\n\n<p>All rights reserved.</p>\n","title":"Siesta automation & reports."});