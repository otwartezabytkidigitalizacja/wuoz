module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		wordpressdeploy: {
      options: {
        backup_dir: "backups/",
        rsync_args: ['--verbose', '--progress', '-rlpt', '--compress', '--omit-dir-times', '--delete'],
        exclusions: ['Gruntfile.js', '.git/', 'tmp/*', 'backups/', 'wp-config.php', 'composer.json', 'composer.lock', 'README.md', '.gitignore', 'package.json', 'node_modules']
      },
      local: {
        "title": "wuoz.dev",
        "database": "wuoz",
        "user": "wp",
        "pass": "wp",
        "host": "localhost",
        "url": "http://wuoz.dev",
        "path": "/Volumes/HDD/vagrant-local/www/wuoz/htdocs/wp-content/uploads/documents/white_card/",
				"ssh_host" : "vagrant@vvv.dev"
      },
      otwarte: {
        "title": "wuoz.otwartezabytki",
        "database": "database_name",
        "user": "database_username",
        "pass": "database_password",
        "host": "database_host",
        "url": "http://wuoz.otwartezabytki.pl",
        "path": "/var/www/wuoz/wp-content/uploads/documents/white_card/",
        "ssh_host": "kuba@centrumcyfrowe.pl"
				// eFeeV8thohza
      }
		},


		// chech our JS
		jshint: {
			options: {
				"bitwise": true,
				"browser": true,
				"curly": false,
				"eqeqeq": true,
				"eqnull": true,
				"esnext": true,
				"immed": true,
				"jquery": true,
				"latedef": true,
				"newcap": true,
				"noarg": true,
				"node": true,
				"strict": false,
				"trailing": true,
				"undef": false,
				"globals": {
					"jQuery": true,
					"alert": true
				}
			},
			all: [
				'gruntfile.js',
                '../javascripts/otwarte-zabytki-lib.js',
                '../javascripts/main.js'
			]
		},

		fixmyjs: {
			options: {
				asi: true
			},
			test: {
				files: [
					'../javascripts/otwarte-zabytki-lib.js'
				]
			}
		},

		// concat and minify our JS
		uglify: {
            dev : {
                options: {
                    beautify: true,
										compress: false,
										mangle: false
                },
                files: {
                    '../javascripts/scripts.min.js': [
						'../javascripts/vendor/custom.modernizr.js',
						'../javascripts/vendor/jquery.wheelzoom.js',
						//'../javascripts/vendor/jquery.js',
						'../javascripts/foundation/foundation.js',
						'../javascripts/foundation/foundation.orbit.js',
						'../javascripts/foundation/foundation.interchange.js',
						'../javascripts/foundation/foundation.abide.js',
						'../javascripts/foundation/foundation.reveal.js',
						'../javascripts/foundation/foundation.tooltips.js',
					//	'../javascripts/foundation/foundation.alerts.js',
						'../javascripts/vendor/gmap3.min.js',
						'../javascripts/vendor/classie.js',
						'../javascripts/vendor/select2.js',
						'../javascripts/vendor/jquery.jcarousel.min.js',
						'../javascripts/otwarte-zabytki-lib.js',
						'../javascripts/main.js'
                    ]
                }
            },
			dist: {
				options: {
					compress: true,
					preserveComments: false
				},
				files: {
					'../javascripts/scripts.min.js': [
						'../javascripts/scripts.min.js'
					]
				}
			}
		},

		compass: {
			dev: {
				options: {
					require: ['zurb-foundation'],
					sourcemap: true,
					sassDir: '../scss/',
					cssDir: '../stylesheets/',
					outputStyle: "compressed",
					noLineComments: true,
					httpPath: "/"
					//config: 'config.rb'
				}
			}
		},

		// watch for changes
		watch: {
            php: {
                options: {
                    livereload: true
                },
                files: ['../**/*.php', '../partials/*.php']
            },
			scss: {
				files: ['../scss/**/*.scss'],
				options: {
					livereload: false
				},
				tasks: [
					'compass:dev',
					'notify:scss'
				]
			},
            //css : {
            //    files :['../stylesheets/app.css'],
            //},
			livereload: {
				files :['../stylesheets/app.css'],
				options: { livereload: true }
			},
			js: {
                options : {
                    livereload: true
                },
				files: [
					'<%= jshint.all %>'
				],
				tasks: [
					'jshint',
					'uglify:dev',
					'notify:js'
				]
			}
		},

		// check your php
		phpcs: {
			application: {
				dir: '../*.php'
			},
			options: {
				bin: '/usr/bin/phpcs'
			}
		},

		// notify cross-OS
		notify: {
			scss: {
				options: {
					title: 'Grunt, grunt!',
					message: 'SCSS is all gravy'
				}
			},
			js: {
				options: {
					title: 'Grunt, grunt!',
					message: 'JS is all good'
				}
			},
			dist: {
				options: {
					title: 'Grunt, grunt!',
					message: 'Theme ready for production'
				}
			}
		},

		clean: {
			dist: {
				src: ['../dist'],
				options: {
					force: true
				}
			}
		},

		copyto: {
			dist: {
				files: [
					{cwd: '../', src: ['**/*'], dest: '../dist/'}
				],
				options: {
					ignore: [
						'../dist{,/**/*}',
						'../doc{,/**/*}',
						'../grunt{,/**/*}',
						'../scss{,/**/*}'
					]
				}
			}
		}
	});

	// Load NPM's via matchdep
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Development task
	grunt.registerTask('default', [
		'jshint',
		'uglify:dev',
		'compass:dev'
	]);

	// Production task
	grunt.registerTask('dist', function() {
		grunt.task.run([
			'jshint',
			'uglify:dist',
			'compass:dev',
			'clean:dist',
			'copyto:dist',
			'notify:dist'
		]);
	});
};
