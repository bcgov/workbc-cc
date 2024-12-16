resource "aws_iam_openid_connect_provider" "eks" {
  url = "https://oidc.eks.ca-central-1.amazonaws.com/id/F6D93A76A741E3210217A7492185D3DA"

  client_id_list = [
    "sts.amazonaws.com",
  ]

  thumbprint_list = ["9e99a48a9960b14926bb7f3b6d8c2c621ce75bda"]
}
