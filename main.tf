# Define the required Terraform provider and version
terraform {
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "~> 3.87.0"
    }
  }
  required_version = ">= 0.14.9"
}

# Configure the Azure provider
provider "azurerm" {
  features {
    resource_group {
          prevent_deletion_if_contains_resources = false
        }
  }
}

# Create an Azure resource group
resource "azurerm_resource_group" "rg" {
  name     = "agng_resource_group"
  location = "West Europe"
}

resource "azurerm_mysql_server" "agng-serveur" {
  name                = "agng-mysqlserver"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name

  administrator_login          = "agng"
  administrator_login_password = "Password.2024"

  sku_name   = "GP_Gen5_2"
  storage_mb = 5120
  version    = "5.7"

  auto_grow_enabled                 = true
  backup_retention_days             = 7
  geo_redundant_backup_enabled      = true
  infrastructure_encryption_enabled = true
  public_network_access_enabled     = true
  ssl_enforcement_enabled           = false
  ssl_minimal_tls_version_enforced  = "TLSEnforcementDisabled"
}

resource "azurerm_mysql_database" "agng-database" {
  name                = "agngdb"
  resource_group_name = azurerm_resource_group.rg.name
  server_name         = azurerm_mysql_server.agng-serveur.name
  charset             = "utf8"
  collation           = "utf8_unicode_ci"
  # prevent the possibility of accidental data loss
 
}

resource "azurerm_public_ip" "agng_public_ip" {
  name                = "agng_public_ip"
  resource_group_name = azurerm_resource_group.rg.name
  location            = azurerm_resource_group.rg.location
  allocation_method   = "Static"
  sku ="Standard"
}

resource "azurerm_network_security_group" "agng_network_security_group" {
  name                = "agng_nsg"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name
  
}

resource "azurerm_network_security_rule" "agng_ssh_rule" {
  name                        = "AllowSSH"
  priority                    = 1001
  direction                   = "Inbound"
  access                      = "Allow"
  protocol                    = "Tcp"
  source_port_range           = "*"
  destination_port_range      = "22"
  source_address_prefix       = "*"
  destination_address_prefix  = "*"
  resource_group_name         = azurerm_resource_group.rg.name
  network_security_group_name = azurerm_network_security_group.agng_network_security_group.name
}

resource "azurerm_virtual_network" "agng_virtual_network" {
  name                = "agng-network"
  address_space       = ["10.0.0.0/16"]
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name
  dns_servers         = ["10.0.0.4", "10.0.0.5"]

}

resource "azurerm_subnet" "agng_subnet" {
  name                 = "agng_subnet"
  resource_group_name  = azurerm_resource_group.rg.name
  virtual_network_name = azurerm_virtual_network.agng_virtual_network.name
  address_prefixes     = ["10.0.2.0/24"]
}

resource "azurerm_network_interface" "agng_ni" {
  name                = "agng-ni"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name

  ip_configuration {
    name                          = "agng_ip_config"
    subnet_id                     = azurerm_subnet.agng_subnet.id
    private_ip_address_allocation = "Dynamic"
    public_ip_address_id = azurerm_public_ip.agng_public_ip.id
  }
}

resource "azurerm_network_interface_security_group_association" "nsg_association" {
  network_interface_id      = azurerm_network_interface.agng_ni.id
  network_security_group_id = azurerm_network_security_group.agng_network_security_group.id

}

resource "azurerm_ssh_public_key" "agng_public_key" {
  name                = "agng_ssh_public_key"
  resource_group_name = azurerm_resource_group.rg.name
  location            = azurerm_resource_group.rg.location
  public_key          = file("id_rsa.pub")
}

resource "azurerm_linux_virtual_machine" "angn_vm" {
  name                  = "agng-vm"
  location              = azurerm_resource_group.rg.location
  resource_group_name   = azurerm_resource_group.rg.name
  network_interface_ids = [azurerm_network_interface.agng_ni.id]
  size                = "Standard_F2"
  admin_username      = "agng"

 admin_ssh_key {
    username = "agng"
    public_key = azurerm_ssh_public_key.agng_public_key.public_key
  }

os_disk {
    caching              = "ReadWrite"
    storage_account_type = "Standard_LRS"
}

source_image_reference {
    publisher = "Canonical"
    offer     = "0001-com-ubuntu-server-jammy"
    sku       = "22_04-lts"
    version   = "latest"
}
}

# resource "azurerm_storage_account" "agng-storage" {
#   name                     = "agngstorage"
#   resource_group_name      = azurerm_resource_group.rg.name
#   location                 = azurerm_resource_group.rg.location
#   account_tier             = "Standard"
#   account_replication_type = "LRS"
# }