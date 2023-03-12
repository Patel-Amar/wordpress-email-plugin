<?php
if (!defined('ABSPATH')){
    exit;
 }

function createForm() {?>
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body width="10em" id="body">
        <script>
            $(document).ready(function() {
                var emailGroups = <?php  
                    define("Database", plugin_dir_path(__FILE__) . "Database.php");
                    require_once(Database);
                    echo json_encode(getAllEmailGroups());
                    ?>;


                const select = document.getElementById("selectOption");
                Object.entries(emailGroups).forEach(entry => {
                    const [key, value] = entry;
                    const option = document.createElement("option");
                    option.value = value;
                    option.innerText = key;
                    select.appendChild(option);
                });
            });
        </script>
        <div id="formSuccess" style="background:green; color:#fff;"></div>
        <div id="formError" style="background: red; color:#fff;"></div>

        <div class="Send-Email" width="10em">
            <h1>Send Email</h1>
            <br>
            <form id="Information">
                <div class="form-group mb-2">
                    <select name="Email_Group[]" class="form-control" id="selectOption" multiple required style="width:75%;">
                    </select>
                </div>
                <br>
                <div class="form-group mb-2">
                    <input name="Subject" type="text" placeholder="Subject" class="form-control" required style="width:100%; padding-bottom:1em; margin-bottom:1em">
                </div>
                <div class="form-group mb-2">
                    <div class="main-content">
                        <div class="text-options-header">
                            <button type="button" class="btn" data-element="bold" id="unclicked" >
                                <i class="fa fa-bold"></i>
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/Bold.png"?> alt="Bold"/>
                            </button>
                            <button type="button" class="btn" data-element="italic" id="italic" >
                                <i class="fa fa-italic"></i>
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/Italic.png"?> alt="Italic"/>
                            </button>
                            <button type="button" class="btn" data-element="underline" id="underline">
                                <i class="fa fa-underline"></i>
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/underline.png"?> alt="Underline"/>
                            </button>
                            <button type="button" class="btn" data-element="link" >
                                <i class="fa-link"></i>
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/Link.png"?> alt="Link"/>
                            </button>
                            <button type="button" class="btn" data-element="insertImage">
                                <i class="fa fa-image"></i>
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/insertImage.png"?> alt="Image"/>
                            </button>

                            <br>

                            <h3 id="font"> Font Size </h3>
                            <button type="button" class="btn" data-element="font-change" id="arrowUp" name = "arrow">
                                <img src=<?php echo WP_PLUGIN_URL ."/mailing-list-mailer/images/up.png"?> alt="up"/>
                            </button>
                            <input type="text" name="arrow" id="fontText" placeholder="1.8">
                            <button type="button" class="btn" data-element="font-change" id="blue"> </button>
                            <button type="button" class="btn" data-element="font-change" id="black"> </button>
                            <button type="button" class="btn" data-element="font-change" id="orange"> </button>
                            <button type="button" class="btn" data-element="font-change" id="red"> </button>
                        </div>

                        <br>

                        <div name="Contents" class="textDiv" id="textBox" placeholder="Email Content" contenteditable="true"> </div>
                        <textarea id="hiddeninput" name="Contents" style="visibility: hidden; height:0px"></textarea>
                    
                    </div>
                    
                </div>
                <div class="form-group">
                    <button type="submit" class="btnS"> Send Email </button>
                </div>
            </form>
        </div>
        <script src= <?php echo WP_PLUGIN_URL . "/mailing-list-mailer/emailContentBox.js" ?>> </script>
        <script>
            $(document).ready(function(){
                $("#selectOption").select2({
                    placeholder: "Select an Email",
                    allowClear: true
                });
            });
        </script>
    </body>
    <style>
        .textDiv {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width:100%; 
            padding-bottom:1em; 
            margin-bottom:1em; 
            border-style: solid; 
            border-radius: .3em; 
            border-width: 1px; 
            color: black; 
            font-size: 1.5em; 
            font-family:Ubuntu;
        }

        img {
            width: 10em;
        }
        #fontText {
            width: 2.5em;
            height: 2em;
        }
        #font {
            margin-bottom: 5px;
        }
        #red {
            background-color: #FF0000;
            margin-top:0px;
            padding-top:0px;
            width: 2em;
            height: 2em;
        }
        #orange {
            background-color: #FFA400;
            margin-top:0px;
            padding-top:0px;
            width: 2em;
            height: 2em;
        }
        #black {
            background-color: black;
            margin-top:0px;
            padding-top:0px;
            width: 2em;
            height: 2em;
        }
        #blue {
            background-color: #0063AA;
            margin-top:0px;
            padding-top:0px;
            width: 2em;
            height: 2em;
        }
        .btn[name="arrow"] {
            margin-top:0px;
            padding-top:0px;
            width: 2em;
            height: 2em;
        }
        img {
            width :100%;
        }
        .btn {
            background-color: white;
            color: #000000;
            cursor: pointer;
            font-weight: bold;
            width:3em; 
            height:3em;
        }
        .btnS{
            width: 100%;
            height: 3em;
            padding-bottom: 1em;
            margin-bottom: 1em;
            background: gray;
            display: inline-block;
            font-size: 1em;
        }
        .btnS:hover{
            background: white;
        }
    </style>
    <script>
        document.getElementById('textBox').addEventListener('keydown', function(e) {
            if (e.key == 'Tab') {
                e.preventDefault();
                var start = this.selectionStart;
                var end = this.selectionEnd;

                this.value = this.value.substring(0, start) +
                "\t" + this.value.substring(end);
                this.selectionStart =
                this.selectionEnd = start + 1;
            }
        });
    </script>
<?php }