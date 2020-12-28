import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ApprouveurComponent } from './approuveur.component';

describe('ApprouveurComponent', () => {
  let component: ApprouveurComponent;
  let fixture: ComponentFixture<ApprouveurComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ApprouveurComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ApprouveurComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
