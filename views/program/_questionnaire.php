

    
    <div class="card">
        <div class="card-body">

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Questions</th>
                  <?php 
                  for($x=1;$x<=5;$x++){
                    echo '<th></th>';
                  }
                  ?>
                  </tr>
                    <?php
                    $i = 1;
                    foreach($quest_likert as $q){
                      echo '<tr><td>'.$i.'. </td><td>'.$q->question_text.'</td>';
                      for($x=1;$x<=5;$x++){
                        echo '<td><input type="radio" /></td>';
                      }
                      echo '</tr>';
                      $i++;
                    }
                    ?>
                    
                </tbody>
            </table>
        </div>
            
        </div>
    </div>
    
<div class="card">
        <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Questions</th>
 
                  </tr>
                    <?php
                    $i = 1;
                    foreach($quest_essay as $q){
                      echo '<tr><td>'.$i.'. </td><td><label>'.$q->question_text.'</label>
                      <div style="margin-top:10px"><textarea rows="5" class="form-control"></textarea></div>
                      </td>';
               
                      echo '</tr>';
                      $i++;
                    }
                    ?>
                    
                </tbody>
            </table>
        </div>
        </div>
    </div>

