<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>

    <script>
let childrenAndIds;
let i = 0;
$.get("getUsers.php", function(data1){
let tempVar = data1.split("&");
tempVar[0] = tempVar[0].split(',');
tempVar[1] = tempVar[1].split(',');
childrenAndIds = tempVar;
console.log(tempVar);
});

$.get("backgroundProcess.php", function(receivedTable){
const elementHolder = document.createElement("div");
elementHolder.id="tableHolder";
document.body.appendChild(elementHolder);
document.getElementById("tableHolder").innerHTML = receivedTable;

})
function updateCompletionStatus(index){
	if(confirm('are you sure?')){
		
			
	let datauser =  document.getElementById(index);
console.log(datauser);


const text = (element) => element == datauser.innerText;
console.log(childrenAndIds);
let numberChildren = childrenAndIds[0].findIndex(text);
document.getElementsByClassName("executorIds")[index - 2].innerText = childrenAndIds[1][numberChildren];
console.log(childrenAndIds[1][numberChildren]);
console.log(numberChildren);
	 $.ajax({
                    type: "POST",
                    url: "workCheckUpdater.php",
                    data: `id=${index}`,
                    success: function(data){
                     console.log(data);
                  
                    }
                    });
	}
}

function jf(index){
document.getElementById(index).innerHTML = `<select onchange='ih(${index})' value='${index}' name='select' id='id${index}'><\/select>`
document.getElementById(index).onclick = '';
let select = document.getElementById('id'+index);
select.innerHTML = `<option >Please select a name.<\/option>`
for ( i = 0; childrenAndIds[0].length > i; i++){
select.innerHTML = select.innerHTML + `<option id=ie${i} >${childrenAndIds[0][i]}<\/option>`
}
}
function ih(index){


let selectBox = '<td class="hobbits" data-userid="' + index + '" onclick="jf(' + index + ')" id="' + index + '">' + document.getElementById('id'+index).value + ' </td>';
document.getElementById(index).innerHTML=`<td>${selectBox}<\/td>`;

let datauser =  document.getElementById(index);
console.log(datauser);

const text = (element) => element == datauser.innerText;

let numberChildren = childrenAndIds[0].findIndex(text);
document.getElementsByClassName("executorIds")[index - 2].innerText = childrenAndIds[1][numberChildren];
console.log(childrenAndIds[1][numberChildren]);
console.log(numberChildren);
 $.ajax({
                    type: "POST",
                    url: "uploaderDB.php",
                    data: `who_id=${childrenAndIds[1][numberChildren]}&id=${index}&hobbit=${datauser.innerText}`,
                    success: function(receivedData){

                        alert(receivedData)
                    }
                    });
				
}
function objectIterator(collection){
let returnedArr = [];

for (let j=0; j < Object.keys(collection).length;j++){
returnedArr.push(collection[j].innerText)
}
let valueToReturn = returnedArr.join()

return valueToReturn;
}

function registerInfo(){



let objectlocations = objectIterator(document.getElementsByClassName("locations"));
let objecthobbits = objectIterator(document.getElementsByClassName("hobbits"));
let objectexecutorsIds = objectIterator(document.getElementsByClassName("executorIds"));
document.getElementById('locationName').value = objectlocations;
document.getElementById('hobbit').value = objecthobbits;
document.getElementById('executorId').value = objectexecutorsIds;
document.getElementById('buttonTransformDiv').innerHTML = '<input type="submit">';
console.log(objectlocations);
console.log(objecthobbits);
console.log(objectexecutorsIds);
}
	</script>
</body>
</html>