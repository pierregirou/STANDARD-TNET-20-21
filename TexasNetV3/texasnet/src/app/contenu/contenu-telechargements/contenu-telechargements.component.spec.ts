import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuTelechargementsComponent } from './contenu-telechargements.component';

describe('ContenuTelechargementsComponent', () => {
  let component: ContenuTelechargementsComponent;
  let fixture: ComponentFixture<ContenuTelechargementsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuTelechargementsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuTelechargementsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
