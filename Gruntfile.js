/* jshint node:true */
'use strict';

module.exports = function( grunt ) {

	// auto load grunt tasks
	require( 'load-grunt-tasks' )( grunt );

	var pluginConfig = {

		// gets the package vars
		pkg: grunt.file.readJSON( 'package.json' ),

		// plugin directories
		dirs: {
			js: 'assets/js',
			css: 'assets/css',
			sass: 'assets/sass',
			images: 'assets/images',
			fonts: 'assets/fonts'
		},

		// svn settings
		svn_settings: {
			path: '../../../../wp_plugins/<%= pkg.name %>',
			tag: '<%= svn_settings.path %>/tags/<%= pkg.version %>',
			trunk: '<%= svn_settings.path %>/trunk',
			exclude: [
				'.editorconfig',
				'.git/',
				'.gitignore',
				'.jshintrc',
				'.sass-cache/',
				'node_modules/',
				'assets/sass/',
				'Gruntfile.js',
				'README.md',
				'package.json',
				'*.zip'
			]
		},

		// javascript linting with jshint
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/admin.js',
				'<%= dirs.js %>/ultimate-modal.js'
			]
		},

		// uglify to concat and minify
		uglify: {
			dist: {
				files: {
					'<%= dirs.js %>/admin.min.js': [
						'<%= dirs.js %>/admin.js'
					],
					'<%= dirs.js %>/ultimate-modal.min.js': [
						'<%= dirs.js %>/jquery.cookie.js',
						'<%= dirs.js %>/ultimate-modal.js'
					]
				}
			}
		},

		// compass and scss
		compass: {
			options: {
				httpPath: '',
				environment: 'production',
				relativeAssets: true,
				noLineComments: true,
				outputStyle: 'compressed'
			},
			dist: {
				options: {
					sassDir: '<%= dirs.sass %>',
					cssDir: '<%= dirs.css %>',
					imagesDir: '<%= dirs.images %>',
					javascriptsDir: '<%= dirs.js %>',
					fontsDir: '<%= dirs.fonts %>'
				}
			}
		},

		// watch for changes and trigger compass, jshint and uglify
		watch: {
			compass: {
				files: [
					'<%= compass.dist.options.sassDir %>/**'
				],
				tasks: ['compass']
			},
			js: {
				files: [
					'<%= jshint.all %>'
				],
				tasks: ['jshint', 'uglify']
			}
		},

		// image optimization
		imagemin: {
			dist: {
				options: {
					optimizationLevel: 7,
					progressive: true
				},
				files: [
					{
						expand: true,
						cwd: '<%= dirs.images %>/',
						src: '**/*.{png,jpg,gif}',
						dest: '<%= dirs.images %>/'
					},
					{
						expand: true,
						cwd: './',
						src: 'screenshot-*.png',
						dest: './'
					}
				]
			}
		},

		// rsync commands used to take the files to svn repository
		rsync: {
			options: {
				args: ['--verbose'],
				exclude: '<%= svn_settings.exclude %>',
				syncDest: true,
				recursive: true
			},
			tag: {
				options: {
					src: './',
					dest: '<%= svn_settings.tag %>'
				}
			},
			trunk: {
				options: {
				src: './',
				dest: '<%= svn_settings.trunk %>'
				}
			}
		},

		// shell command to commit the new version of the plugin
		shell: {
			// Remove delete files.
			svn_remove: {
				command: 'svn st | grep \'^!\' | awk \'{print $2}\' | xargs svn --force delete',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn_settings.path %>'
					}
				}
			},
			// Add new files.
			svn_add: {
				command: 'svn add --force * --auto-props --parents --depth infinity -q',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn_settings.path %>'
					}
				}
			},
			// Commit the changes.
			svn_commit: {
				command: 'svn commit -m "updated the plugin version to <%= pkg.version %>"',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn_settings.path %>'
					}
				}
			}
		}
	};

	// initialize grunt config
	// --------------------------
	grunt.initConfig( pluginConfig );

	// register tasks
	// --------------------------

	// default task
	grunt.registerTask( 'default', [
		'jshint',
		'compass',
		'uglify'
	] );

	// deploy task
	grunt.registerTask( 'deploy', [
		'default',
		'rsync:tag',
		'rsync:trunk',
		'shell:svn_remove',
		'shell:svn_add',
		'shell:svn_commit'
	] );
};
