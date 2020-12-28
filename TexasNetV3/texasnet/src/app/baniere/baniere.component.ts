import { Component, OnInit } from '@angular/core';
import { ImageService } from "../services/images.service";

@Component({
  selector: 'app-baniere',
  templateUrl: './baniere.component.html',
  styleUrls: ['./baniere.component.css']
})
export class BaniereComponent implements OnInit {

  constructor(public imageService:ImageService) { }

  ngOnInit() {
  }

}
