<?php
    session_start();

    //세션이 존재하지 않을 때 == 로그인이 아직 안 되어 있을 때
    if(!isset($_SESSION['userid'])) 
    {
        header ('Location: ./main.php');
    }
    //세션이 존재할 때 == 로그인이 되어 있을 때
    $id = $_SESSION['userid'];

    require('db.php');
    
    $check="SELECT * FROM user_info WHERE userid='$id'";
    $result=$mysqli->query($check); 
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $boothname = $row['boothname'];
    $isAdmin = $row['admin'];

    //일반 부스 운영자가 들어왔을 때: 자기 위치로 이동
    if(!$isAdmin)
    {
        header ('Location: ./main.php');
    }

    $amount=$_POST['amount'];
    $rfid = $_POST['rfid'];

    ?>

<script type= "text/javascript">
        var con_test = confirm("<?php echo $idnum," 고객에게 ",$price,"원 만큼 결제합니다"?>.");
        if(con_test == true)
        {
            <?php
            //먼저 해당 rfid가 가입되어 있는지 검사
            $check="SELECT *from account_info WHERE rfid='$rfid'";
            $result=$mysqli->query($check);
            if($result->num_rows==1)
            {
                //한 개 계정이 검출
                $current="SELECT * FROM account_info WHERE rfid='$rfid'";
                $result2=$mysqli->query($current); 
                $row2=$result2->fetch_array(MYSQLI_ASSOC);
                $idnum = $row2['idnumber'];
                $money=$row2['balance'];
                $total = $money + $amount;

                $charge=mysqli_query($mysqli,"UPDATE account_info SET balance='$total' WHERE rfid='$rfid'");
                unset($_POST);
                if($charge)
                {
                    echo $idnum," 계좌에 ",$amount,"원 만큼 충전하여 현재 잔액은 ",$total,"원입니다";
                    echo "<br><button onclick=\"location.href='main.php'\"> 돌아가기 </button>";
                }
                else
                    echo "<br><button onclick=\"location.href='main.php'\"> 충전 실패, 돌아가기 </button>";
            }
            else
            {
                echo "등록되지 않은 학생증입니다.";
                echo "<br><button onclick=\"location.href='main.php'\"> 돌아가기 </button>";
                exit();
            }
            ?>
        }
        else
            location.replace("charge.php");
    </script>
