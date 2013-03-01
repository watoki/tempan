<?php
namespace spec\watoki\tempan;

use watoki\tempan\Renderer;
use watoki\collections\Collection;

class ComplexTest extends Test {

    public function testExampleFromReadme() {
        $model = json_decode('{
            "actor": {
                "name": "John Wayne",
                "url": {
                    "caption": "johnwayne.com",
                    "href": "http://johnwayne.com"
                },
                "isAmericanLegend": true,
                "movies": {
                    "isEmpty": false,
                    "isNotEmpty": true,
                    "isMany": true,
                    "count": 3,
                    "movie": [
                        {
                            "title": "Legend of the Lost",
                            "year": 1957
                        },
                        {
                            "title": "The Alamo",
                            "year": 1960
                        },
                        {
                            "title": "True Grit",
                            "year": 1969
                        }
                    ]
                }
            }
        }');

        $markup = '
        <html>
            <body>
                <div property="actor">
                    <div>Name: <span property="name">Some Name</span></div>
                    <div>Website: <a property="url" href="#"><span property="caption">example.com</span></a></div>
                    <div property="isAmericanLegend">This actor is an american legend</div>

                    <div property="movies">
                        <p property="isEmpty">He did not star in any movies</p>
                        <div property="isNotEmpty">
                            <p>He starred in <span property="count">X</span> movie<span property="isMany">s</span></p>

                            <ul>
                                <li property="movie">
                                    <span property="title">Movie Title</span> (<span property="year">19XX</span>)
                                </li>
                                <li property="movie">
                                    <span property="title">Another Movie</span> (<span property="year">19XX</span>)
                                </li>
                                <li property="movie">
                                    <span property="title">Even more movies</span> (<span property="year">19XX</span>)
                                </li>
                                <li property="movie">
                                    <span property="title">Some Movie Title</span> (<span property="year">19XX</span>)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </body>
        </html>';

        $renderer = new Renderer($markup);
        $rendered = $renderer->render($model);

        $expected = '
        <html>
            <body>
                <div property="actor">
                    <div>Name: <span property="name">John Wayne</span></div>
                    <div>Website: <a property="url" href="http://johnwayne.com"><span property="caption">johnwayne.com</span></a></div>
                    <div property="isAmericanLegend">This actor is an american legend</div>

                    <div property="movies">
                        <div property="isNotEmpty">
                            <p>He starred in <span property="count">3</span> movie<span property="isMany">s</span></p>
                            <ul>
                                <li property="movie">
                                    <span property="title">Legend of the Lost</span> (<span property="year">1957</span>)
                                </li>
                                <li property="movie">
                                    <span property="title">The Alamo</span> (<span property="year">1960</span>)
                                </li>
                                <li property="movie">
                                    <span property="title">True Grit</span> (<span property="year">1969</span>)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </body>
        </html>';

        $this->assertEquals($this->clean($expected), $this->clean($rendered));
    }

}
