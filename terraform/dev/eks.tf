resource "aws_eks_cluster" "workbc-cluster" {
  name = "workbc-cluster"
  access_config {
    authentication_mode = "API_AND_CONFIG_MAP"
  }
}
