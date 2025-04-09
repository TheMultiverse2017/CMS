<style>
    #sectionTextarea{
        width: 100%;
        height: auto;
    }
</style>
<div class="container">
    <div id="sectionMainDiv">
        <input class="form-control my-3" name="sectionTitle[]" type="text" id="sectionTitle">
        <textarea class="form-control my-3" name="section[]" id="sectionTextarea" ></textarea>
    </div>
    <button class="btn btn-primary" id="btnAddNewSection" style="width: 100%">Add New section</button>
</div>

<script>
    $('#btnAddNewSection').on('click',function(){
        alert("sjdk");
    })
</script>