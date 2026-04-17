<?php

return [

    // Finance (will be expanded)
    'finance' => [
        'orders' => [
            'description' => 'Orders reflect the fact that users have placed paid services or products in the system. They record selected items, cost, payment status and are linked to users and training programs. Orders are used to track sales, access to content and financial analytics.',
        ],
        'subscriptionPlans' => [
            'description' => 'Tariff plans are a system of subscriptions and access levels for online school users. They allow you to configure validity periods, prices, currencies, plan content, and restrictions. They are used to manage access to courses, modules, and additional platform features.',
        ],
        'coursePrices' => [
            'description' => 'Online School Course Pricing — Manage tuition for each course and currency. Allows you to set base, promotional, and legacy prices, validity period, display order, and activity. This is used to accurately calculate prices, discounts, and display prices on the storefront.',
        ],
        'bundlePrices' => [
            'description' => 'Online School Bundle Pricing — Manage the pricing of packages containing multiple courses. Allows you to set the base and promotional prices, currency, validity period, sorting, and activity. This feature is used to set up special offers, promotions, and accurately calculate the final price.',
        ],
        'currencies' => [
            'description' => 'Currencies is a multicurrency module for working with different currencies in the system. It supports automatic exchange rate updates from an external provider, manual adjustments, change history, and the use of exchange rates in pricing calculations, orders, reports, and analytics.',
        ],
    ],

    // Page builder (will be expanded)
    'pageBuilder' => [
        'home' => [
            'description' => 'The page builder is designed to manage the content of pages of the public part of the site without changing the code. It allows you to update text and media blocks, create the structure of pages and control the display of sections. Currently, editing the content of sections of the main page has been implemented.',
        ],
    ],

    // System
    'system' => [
        'users' => [
            'description' => 'Users represent registered members of the system who have access to the functionality of the site or platform. They can have different roles and permissions, participate in training, leave comments, view content and interact with services.',
        ],
        'roles' => [
            'description' => 'Roles are used to differentiate user access rights in the system. Based on the spatie/laravel-permission package, roles define the set of permissions available to the user and allow flexible management of capabilities in the administrative and user parts of the application.',
        ],
        'permissions' => [
            'description' => 'Permissions define specific actions that a user can perform on the system. Based on the spatie/laravel-permission package, they allow access to application functions and entities to be finely tuned, assigned to roles or directly to users, and provide flexible access control.',
        ],
        'settings' => [
            'description' => 'Settings are designed to control the values of system parameters through the user interface. They allow you to change existing configurations without adding new parameters, providing convenient and safe adjustments to application behavior without interfering with the code.',
        ],
        'parameters' => [
            'description' => 'System parameters are used to expand and flexibly configure the application. They are added through the administrative panel, saved in the database and allow you to control the behavior of the system without changing the code, including values, modes and additional configurations.',
        ],
        'logs' => [
            'description' => 'Logs contain technical information about the operation of the system, including events from Laravel and the Nginx web server. They allow you to track errors, requests and application actions, view them in the administrative panel, download them for analysis and clear them if necessary.',
        ],
        'phpinfo' => [
            'title' => 'PHP info',
            'description' => 'PHP Info provides detailed information about the PHP configuration on the server. In the administrative panel, you can view information about the PHP version, connected extensions, environment settings and execution parameters, which helps in diagnosing, configuring and maintaining the correct operation of the application.',
        ],
        'composer' => [
            'title' => 'Composer json',
            'description' => 'Composer is used to manage PHP project dependencies. In the administrative panel you can view the composer.json file, which contains a list of libraries, versions and startup settings. This allows you to control the composition of the project and simplifies the maintenance and updating of the application.',
        ],
        'package' => [
            'title' => 'Package json',
            'description' => 'Package is used to manage dependencies and scripts for the front-end part of the project. In the administrative panel, you can view the package.json file, which contains a list of packages, versions, build and configuration commands, which simplifies the control and maintenance of the client part of the application.',
        ],
        'env' => [
            'title' => '.env',
            'description' => 'Env contains environment variables that define key parameters for the application. In the administrative panel, you can view the values of the .env file, including settings for connecting to the database, services and environment, which makes it easier to diagnose and control the system configuration.',
        ],
        'backups' => [
            'description' => 'Database backups are used to save backup copies of system data. In the administrative panel you can create backup copies, download them and, if necessary, restore the database, which ensures quick restoration of the application.',
        ],
        'fileBackup' => [
            'description' => 'Site Backup and Restore allows you to create complete backups of your project files and structure. Through the administrative panel, you can start archiving, download copies and restore the site in case of failures or errors, ensuring the safety of data and stable operation of the system.',
        ],
        'components' => [
            'description' => 'Viewing and editing component code allows you to manage files in the public part of the site through the built-in editor in the administrative panel. This makes it possible to make changes to the code of components, save them and quickly update the appearance and behavior of the interface without access to the server.',
        ],
        'robot' => [
            'description' => 'Editing the robots.txt file allows you to manage the rules for indexing your site by search engines. Through the administrative panel you can change the contents of a file, save edits and control access of search robots to sections of the site without direct intervention in the file system.',
        ],
        'sitemapTitle' => [
            'description' => 'Sitemap.xml generation is used to generate a sitemap containing a list of available pages and their updates. The file helps search engines index content faster, track site structure and correctly take into account changes, increasing the quality of SEO and visibility of resources.',
        ],
        'reports' => [
            'description' => 'Reports allow you to view database data in the form of tables with all fields and values. They make it possible to generate downloads in CSV, Excel, Word, PDF and ZIP formats, download them for analysis, storage and use in reporting.',
        ],
    ],

    // Blog
    'blog' => [
        'rubrics' => [
            'description' => 'Blog headings are used to structure and group materials by topic. They help organize articles, make navigation easier for readers, and improve content discovery. Headings allow you to form thematic sections and logically organize publications.',
        ],
        'sections' => [
            'description' => 'Used to vertically divide a page into separate semantic blocks. Each section contains posts grouped according to specified display rules. There can be one or several sections, which allows flexible management of the page structure and content presentation.',
        ],
        'posts' => [
            'description' => 'Blog posts are publications with text and media content designed to inform and engage an audience. They can relate to headings and sections, have publication statuses, metadata and are used to generate news and thematic materials on the site.',
        ],
        'tags' => [
            'description' => 'Blog tags are used to further categorize posts by key topics and concepts. They help connect materials with each other, simplify navigation and search for content, and also allow you to create collections of articles based on tags of interest.',
        ],
        'comments' => [
            'description' => 'Blog comments allow users to leave feedback and discuss the content of the site. The polymorphic structure of comments makes it possible to link them to various types of materials, such as posts or other entities, support replies and form tree-like threads of discussions.',
        ],
        'banners' => [
            'description' => 'Banners are used to visually highlight important information and attract users attention. They can contain images, text and links, are placed in different areas of the site and are managed by display conditions, activity and display order.',
        ],
        'videos' => [
            'description' => 'Videos are used to post educational, informational and media content on the site. They can be local or external, relate to headings and sections, have publication statuses and customizable display options, providing a visual and convenient presentation of materials.',
        ],
        'charts' => [
            'description' => 'Graphs are used to visually display statistics based on views and likes. They allow you to visually analyze the popularity of materials, track the dynamics of user activity and compare indicators between various website entities.',
        ],
    ],

    // Online school
    'education' => [
        'instructors' => [
            'description' => 'Online school instructors are specialists who accompany students at every stage of their education. He helps students cope with difficulties, answers questions, monitors assignments and motivates them to achieve results.',
        ],
        'hashtags' => [
            'description' => 'Online school hashtags are a tool for naming and categorizing content that allows you to group posts by topic, interest, or event. They help users find content related to certain topics and participate in discussions.',
        ],
        'learningCategories' => [
            'description' => 'Allows you to gain knowledge in an area of interest, for example, learn a new profession or learn something about your favorite hobby. Students study educational materials on their own without direct contact with the teacher. The program consists of recorded lessons, after watching which students complete assignments.',
        ],
        'bundles' => [
            'description' => 'Course bundles combine multiple educational programs into a single training package. They allow you to take related courses sequentially or in parallel, provide shared access to materials and can have a single cost, training time and enrollment conditions for students.',
        ],
        'courses' => [
            'description' => 'A distance learning format that allows you to gain knowledge and do homework on the Internet. They involve lectures in video format, live or recorded, interactive tests, file sharing, communication with the teacher and classmates, etc.',
        ],
        'modules' => [
            'description' => 'Digital resources, components of courses that provide all the necessary information, resources and tools to learn a specific subject or skill. They are typically interactive, rich in multimedia, and accessible through a learning management system (LMS).',
        ],
        'lessons' => [
            'description' => 'Educational process carried out remotely via the Internet. Students study from home: watch lectures on an online platform, study new topics in various subjects, do homework, and if something remains unclear, ask questions to teachers. Class formats may vary.',
        ],
        'assignments' => [
            'description' => 'Online school assignments are homework assignments that students must complete independently outside of class. They perform the following functions: improve the assimilation of the material - the student spends time doing independent work, immersing himself deeper into the topic; discipline and motivate.',
        ],
        'courseSchedules' => [
            'description' => 'Stream scheduling is a way to structure your schedule and fit more activities into a limited amount of time. For example, classes are held in 2 streams, which run in parallel at the same time. Each participant can choose which stream classes he wants to attend.',
        ],
        'cohortEnrollments' => [
            'description' => 'Registration for a stream is the registration of a student for a specific course run with specified start and end dates, class schedule and study group. Streams allow you to run one course several times, manage student enrollments, access to materials, learning statuses and completion results.',
        ],
        'enrollments' => [
            'description' => 'Enrollment in online school streams is the process of officially confirming a student\'s participation in a specific course stream. Enrollment occurs after registration or payment, records the status of training, opens access to lessons, materials, schedule and allows you to track progress and learning results.',
        ],
        'quizzes' => [
            'description' => 'Interactive tests to test students\' knowledge of completed topics and lessons. They are used to consolidate material, assess the level of mastery, identify gaps and self-test. May include scoring, time limits, number of attempts and be taken into account when forming the final learning result.',
        ],
        'quizQuestions' => [
            'description' => 'Quizzes designed to test students\' knowledge on specific topics. Questions may have different answer types, difficulty levels, and scores. Used to assess understanding of the material, consolidate knowledge and formulate results.',
        ],
        'quizAnswers' => [
            'description' => 'Answer options are used in quiz questions to select the correct solution. They can be single or multiple, have one or more correct answers, assigned points, and order of display. They are used to assess knowledge and accurately calculate the results of completing a quiz.',
        ],
        'quizAttempts' => [
            'description' => 'Quizzes attempts reflect the number of times a student can take the test. They allow you to retest your knowledge, improve your results, and reinforce the material. Attempts may be limited in number, time or conditions and are taken into account when calculating the final grade.',
        ],
        'quizAttemptItems' => [
            'description' => 'Quiz attempts record each answer a student enters as part of the test. They allow you to take into account correct and incorrect answers, execution time and sequence of actions. Used to analyze knowledge, calculate points and generate the final result.',
        ],
    ],
];
