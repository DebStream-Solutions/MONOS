# README

<style>
.hidden-content {
  display: none;
}
nav a {
  text-decoration: none;
  color: blue;
  margin-right: 10px;
}
</style>

<nav>
  <a href="javascript:void(0);" onclick="showContent('label1')">Label 1</a> |
  <a href="javascript:void(0);" onclick="showContent('label2')">Label 2</a> |
  <a href="javascript:void(0);" onclick="showContent('label3')">Label 3</a>
</nav>

<div id="label1" class="hidden-content">
  ## Label 1
  Content for label 1. This section will be shown when you click the "Label 1" link in the navigation bar.
</div>

<div id="label2" class="hidden-content">
  ## Label 2
  Content for label 2. This section will be shown when you click the "Label 2" link in the navigation bar.
</div>

<div id="label3" class="hidden-content">
  ## Label 3
  Content for label 3. This section will be shown when you click the "Label 3" link in the navigation bar.
</div>

<script>
function showContent(label) {
  document.querySelectorAll('.hidden-content').forEach(div => div.style.display = 'none');
  document.getElementById(label).style.display = 'block';
}
</script>
